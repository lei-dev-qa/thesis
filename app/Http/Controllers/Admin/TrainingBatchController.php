<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application\Application;
use App\Models\Training\TrainingBatch;
use App\Models\Training\TrainingSchedule;
use App\Models\Training\TrainingResult;
use App\Notifications\Training\TrainingScheduleNotification;
use Illuminate\Http\Request;

class TrainingBatchController extends Controller
{
    /**
     * Display a listing of training batches
     */
    public function index(Request $request)
    {
        $query = TrainingBatch::with([
            'applications' => function($q) {
                $q->where('application_type', 'TWSP');
            },
            'applications.user',
            'trainingSchedule'
        ])
        ->withCount([
            'applications as enrolled_count' => function ($q) {
                $q->where('training_status', Application::TRAINING_STATUS_ENROLLED)
                  ->where('application_type', 'TWSP');
            },
            'applications as completed_count' => function ($q) {
                $q->where('training_status', Application::TRAINING_STATUS_COMPLETED)
                  ->where('application_type', 'TWSP');
            },
            'applications as failed_count' => function ($q) {
                $q->where('training_status', Application::TRAINING_STATUS_FAILED)
                  ->where('application_type', 'TWSP');
            },
        ])
        ->where('status', '!=', TrainingBatch::STATUS_COMPLETED)
        ->orderBy('nc_program')
        ->orderBy('batch_number');

        $batches = $query->get();

        $backoutApplicants = Application::where('status', Application::STATUS_APPROVED)
            ->whereNull('training_batch_id')
            ->where('training_status', Application::TRAINING_STATUS_ENROLLED)
            ->where('application_type', 'TWSP') 
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'total_enrolled' => Application::where('status', Application::STATUS_APPROVED)
                ->where('training_status', Application::TRAINING_STATUS_ENROLLED)
                ->where('application_type', 'TWSP') 
                ->count(),
            'total_batches' => $batches->count(),
            'full_batches' => $batches->where('is_full', true)->count(),
            'backout_count' => $backoutApplicants->count(),
        ];

        return view('admin.trainees.index', compact('batches', 'stats', 'backoutApplicants'));
    }

    /**
     * Display the specified training batch
     */
    public function show(TrainingBatch $batch)
    {
        $batch->load([
            'applications' => function($q) {
                $q->where('application_type', 'TWSP');
            },
            'applications.user',
            'applications.trainingResult',
            'trainingSchedule'
        ]);

        $completedCount = $batch->applications->filter(function($application) {
            return $application->training_status === Application::TRAINING_STATUS_COMPLETED;
        })->count();

        $failedCount = $batch->applications->filter(function($application) {
            return $application->training_status === Application::TRAINING_STATUS_FAILED;
        })->count();

        $availableApplicants = Application::where('status', Application::STATUS_APPROVED)
            ->where('application_type', 'TWSP')
            ->whereNull('training_batch_id')
            ->where('title_of_assessment_applied_for', $batch->nc_program)
            ->with('user')
            ->orderBy('surname')
            ->orderBy('firstname')
            ->get();

        return view('admin.trainees.show', compact('batch', 'completedCount', 'failedCount', 'availableApplicants'));
    }

    /**
     * Mark batch as completed
     */
    public function complete(TrainingBatch $batch)
    {
        $totalTrainees = $batch->applications()->count();
        $completedOrFailed = $batch->applications()
            ->whereIn('training_status', [
                Application::TRAINING_STATUS_COMPLETED,
                Application::TRAINING_STATUS_FAILED
            ])
            ->count();

        if ($totalTrainees !== $completedOrFailed) {
            return redirect()->back()->with('error', 'All trainees must have a result (Completed or Failed) before marking batch as done.');
        }

        $batch->update(['status' => TrainingBatch::STATUS_COMPLETED]);

        return redirect()->route('admin.training-batches.index')->with('success', 'Batch ' . $batch->batch_number . ' has been marked as completed and moved to history.');
    }

    /**
     * Add applicant to training batch
     */
    public function addApplicant(Request $request, TrainingBatch $batch)
    {
        if ($batch->status === TrainingBatch::STATUS_COMPLETED) {
            return back()->with('error', 'Cannot add applicants to a completed batch.');
        }

        if ($batch->is_full) {
            return back()->with('error', 'This batch is already full.');
        }

        $request->validate([
            'application_id' => 'required|exists:applications,id'
        ]);

        $application = Application::findOrFail($request->application_id);

        if ($application->training_batch_id) {
            return back()->with('error', 'This applicant is already enrolled in another batch.');
        }

        if ($application->status !== Application::STATUS_APPROVED) {
            return back()->with('error', 'Only approved applicants can be enrolled.');
        }

        // Refresh the batch to ensure we have the latest trainingSchedule relationship
        $batch->load('trainingSchedule');

        // Determine the training status based on whether schedule exists
        $trainingStatus = $batch->trainingSchedule 
            ? Application::TRAINING_STATUS_ONGOING 
            : Application::TRAINING_STATUS_ENROLLED;

        $application->update([
            'training_batch_id' => $batch->id,
            'training_status' => $trainingStatus,
            'training_schedule_id' => $batch->trainingSchedule ? $batch->trainingSchedule->id : null,
        ]);

        TrainingResult::create([
            'application_id' => $application->id,
            'training_batch_id' => $batch->id,
            'training_schedule_id' => $batch->trainingSchedule ? $batch->trainingSchedule->id : null,
            'result' => TrainingResult::RESULT_ONGOING, 
        ]);

        return back()->with('success', 'Applicant added to batch successfully.');
    }

    /**
     * Remove applicant from training batch
     */
    public function removeApplicant(TrainingBatch $batch, Application $application)
    {
        if ($batch->status === TrainingBatch::STATUS_COMPLETED) {
            return back()->with('error', 'Cannot remove applicants from a completed batch.');
        }

        if ($application->training_batch_id !== $batch->id) {
            return back()->with('error', 'This applicant is not in this batch.');
        }

        $application->update([
            'training_batch_id' => null,
            'training_schedule_id' => null,
            'training_completed_at' => null,
            'training_remarks' => null,
        ]);
        $application->training_status = 'enrolled';
        $application->trainingResult()->delete();

        if ($batch->is_full) {
            $batch->update(['status' => TrainingBatch::STATUS_ENROLLING]);
        }

        return back()->with('success', 'Applicant removed from batch successfully.');
    }

    /**
     * Bulk complete training for multiple applications
     */
    public function bulkComplete(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'training_remarks' => 'nullable|string|max:500',
        ]);

        $applications = Application::whereIn('id', $request->application_ids)->get();

        foreach ($applications as $application) {
            $application->update([
                'training_status' => Application::TRAINING_STATUS_COMPLETED,
                'training_completed_at' => now(),
                'training_remarks' => $request->input('training_remarks'),
            ]);

            $application->trainingResult()->update([
                'result' => TrainingResult::RESULT_COMPLETED,
                'completed_at' => now(),
                'remarks' => $request->input('training_remarks'),
                'evaluated_by' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', count($applications) . ' trainees marked as completed.');
    }

    /**
     * Bulk fail training for multiple applications
     */
    public function bulkFail(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'training_remarks' => 'required|string|max:500',
        ]);

        $applications = Application::whereIn('id', $request->application_ids)->get();

        foreach ($applications as $application) {
            $application->update([
                'training_status' => Application::TRAINING_STATUS_FAILED,
                'training_remarks' => $request->input('training_remarks'),
            ]);

            $application->trainingResult()->update([
                'result' => TrainingResult::RESULT_FAILED,
                'completed_at' => now(),
                'remarks' => $request->input('training_remarks'),
                'evaluated_by' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', count($applications) . ' trainees marked as failed.');
    }

    /**
     * View training progress
     */
    public function progress(Request $request)
    {
        $status = $request->query('status', 'enrolled');
        
        $applications = Application::where('status', Application::STATUS_APPROVED)
            ->where('training_status', $status)
            ->with(['user:id,name,email', 'trainingSchedule'])
            ->orderBy('training_completed_at', 'desc')
            ->paginate(15);

        return view('admin.training.progress', compact('applications', 'status'));
    }

    /**
     * View training history
     */
    public function history(Request $request)
    {
        $ncProgram = $request->query('nc_program');
        $status = $request->query('status');

        $query = TrainingBatch::where('status', TrainingBatch::STATUS_COMPLETED)
            ->with([
                'applications.user',
                'trainingSchedule'
            ])
            ->withCount([
                'applications as completed_count' => function ($q) {
                    $q->where('training_status', Application::TRAINING_STATUS_COMPLETED);
                },
                'applications as failed_count' => function ($q) {
                    $q->where('training_status', Application::TRAINING_STATUS_FAILED);
                },
            ])
            ->orderBy('nc_program')
            ->orderBy('batch_number');

        if ($ncProgram) {
            $query->where('nc_program', $ncProgram);
        }

        $batches = $query->get();

        $availablePrograms = TrainingBatch::where('status', TrainingBatch::STATUS_COMPLETED)
            ->distinct()
            ->pluck('nc_program')
            ->sort()
            ->values();

        return view('admin.history.training.index', compact('batches', 'availablePrograms', 'ncProgram', 'status'));
    }

    /**
     * Show training history for specific batch
     */
    public function historyBatch(TrainingBatch $batch)
    {
        if ($batch->status !== TrainingBatch::STATUS_COMPLETED) {
            return redirect()->route('admin.training-batches.history')
                ->with('error', 'This batch is not yet completed.');
        }

        $batch->load([
            'applications.user',
            'applications.trainingResult',
            'trainingSchedule'
        ]);

        $completedCount = $batch->applications->filter(function($application) {
            return $application->training_status === Application::TRAINING_STATUS_COMPLETED;
        })->count();

        $failedCount = $batch->applications->filter(function($application) {
            return $application->training_status === Application::TRAINING_STATUS_FAILED;
        })->count();

        $totalTrainees = $batch->applications->count();
        $passRate = $totalTrainees > 0 ? round(($completedCount / $totalTrainees) * 100, 2) : 0;

        return view('admin.history.training.show', compact(
            'batch', 
            'completedCount', 
            'failedCount', 
            'totalTrainees', 
            'passRate'
        ));
    }
}
