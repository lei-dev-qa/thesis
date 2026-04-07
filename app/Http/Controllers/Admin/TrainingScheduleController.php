<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application\Application;
use App\Models\Training\TrainingBatch;
use App\Models\Training\TrainingSchedule;
use App\Notifications\Training\TrainingScheduleNotification;
use Illuminate\Http\Request;

class TrainingScheduleController extends Controller
{
    /**
     * Display a listing of training schedules
     */
    public function index(Request $request)
    {
        $ncProgram = $request->query('nc_program');
        
        $schedules = TrainingSchedule::query()
            ->when($ncProgram, fn($q) => $q->where('nc_program', $ncProgram))
            ->orderBy('nc_program')
            ->orderBy('start_date')
            ->get();

        $availablePrograms = TrainingSchedule::distinct()->pluck('nc_program')->sort();
        
        return view('admin.schedules.index', compact('schedules', 'availablePrograms', 'ncProgram'));
    }

    /**
     * Store a newly created training schedule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_batch_id' => 'required|exists:training_batches,id',
            'nc_program' => 'required|string|max:255',
            'schedule_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'days' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1|max:100',
            'venue' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (strtotime($validated['end_time']) <= strtotime($validated['start_time'])) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        $validated['status'] = TrainingSchedule::STATUS_ACTIVE;

        $schedule = TrainingSchedule::create($validated);

        $batch = TrainingBatch::find($validated['training_batch_id']);
        $batch->update(['status' => TrainingBatch::STATUS_SCHEDULED]);

        Application::where('training_batch_id', $batch->id)
            ->update([
                'training_schedule_id' => $schedule->id,
                'training_status' => Application::TRAINING_STATUS_ONGOING
            ]);

        return redirect()->route('admin.training-batches.index')->with('success', 'Training schedule created successfully for Batch ' . $batch->batch_number);
    }


    /**
     * Update the specified training schedule
     */
    public function update(Request $request, TrainingSchedule $schedule)
    {
        $validated = $request->validate([
            'schedule_name' => 'required|string|max:255',
            'schedule_type' => 'required|in:regular,weekend',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'days' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1|max:100',
            'venue' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $schedule->update($validated);

        return redirect()->back()->with('success', 'Training schedule updated successfully.');
    }

    /**
     * Remove the specified training schedule
     */
    public function destroy(TrainingSchedule $schedule)
    {
        $schedule->delete();
        
        return redirect()->back()->with('success', 'Training schedule deleted successfully.');
    }

    /**
     * Get training schedule for editing
     */
    public function edit(TrainingSchedule $schedule)
    {   
        return response()->json($schedule);
    }

    /**
     * Send schedule notifications to all applicants
     */
    public function sendNotifications(TrainingSchedule $schedule)
    {
        $applications = $schedule->applications()->with('user')->get();
        
        if ($applications->isEmpty()) {
            return back()->with('error', 'No applicants assigned to this training schedule.');
        }

        if ($schedule->schedule_notifications_sent_at) {
            return back()->with('warning', 'Schedule notifications have already been sent on ' . 
                $schedule->schedule_notifications_sent_at->format('F d, Y g:i A') . 
                '. Are you sure you want to send them again?');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($applications as $application) {
            try {
                $application->user->notify(new TrainingScheduleNotification($schedule, $application));
                $successCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to send training schedule notification to applicant ID: ' . $application->id . '. Error: ' . $e->getMessage());
                $failCount++;
            }
        }

        $schedule->update([
            'schedule_notifications_sent_at' => now()
        ]);

        if ($failCount > 0) {
            $message = "Schedule notifications sent to {$successCount} applicants. Failed to send to {$failCount} applicants.";
        } else {
            $message = "Schedule notifications sent successfully to all {$successCount} applicants.";
        }

        return back()->with('success', $message);
    }



}
