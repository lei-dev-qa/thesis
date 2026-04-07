<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application\Application;
use Illuminate\Support\Facades\DB;
use App\Models\Training\TrainingBatch;
use App\Models\Training\TrainingSchedule;
use App\Models\Training\TrainingResult;
use App\Models\Assessment\AssessmentBatch;
use App\Models\Assessment\AssessmentResult;
use App\Notifications\Application\ApplicationApprovedNotification;
use App\Models\EnrollmentArchive;
use Illuminate\Validation\Rule;
use App\Models\EmploymentRecord;
use App\Models\Assessment\AssessmentCocResult;
use App\Notifications\Training\TrainingScheduleNotification;

class AdminController extends Controller
{
    // Admin Dashboard
   // Replace the existing dashboard() method in app/Http/Controllers/AdminController.php

    public function dashboard(Request $request)
    {
        $selectedYear = $request->get('year', now()->year);

        $data = [
            'overview' => $this->getOverviewData(),
            'applicant' => $this->getApplicantAnalytics(),
            'training' => $this->getTrainingAnalytics(),
            'assessment' => $this->getAssessmentAnalytics(),
            'employment' => $this->getEmploymentAnalytics(),
            'volume' => $this->getAssessmentVolumeAnalytics($selectedYear),
        ];
        
        return view('admin.dashboard', $data);
    }

    private function getOverviewData()
    {
        $totalApplications = Application::count();
        $pendingApplications = Application::where('status', 'pending')->count();
        $activeTrainingBatches = TrainingBatch::whereIn('status', ['active', 'ongoing', 'scheduled'])->count();
        
        // Calculate overall competency rate
        $totalAssessments = AssessmentResult::count();
        $competentCount = AssessmentResult::where('result', 'Competent')->count();
        $competencyRate = $totalAssessments > 0 ? round(($competentCount / $totalAssessments) * 100, 1) : 0;
        
        return [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'active_training_batches' => $activeTrainingBatches,
            'competency_rate' => $competencyRate,
        ];
    }

    private function getApplicantAnalytics()
    {
        // Define the 5 specific programs
        $programs = [
            'EVENTS MANAGEMENT SERVICES NC III',
            'TOURISM PROMOTION SERVICES NC II',
            'BOOKKEEPING NC III',
            'PHARMACY SERVICES NC III',
            'VISUAL GRAPHIC DESIGN NC III'
        ];
        
        // Get application counts for each program
        $programData = [];
        $totalApplications = 0;
        
        foreach ($programs as $program) {
            $count = Application::where('title_of_assessment_applied_for', $program)->count();
            $programData[] = [
                'name' => $program,
                'count' => $count
            ];
            $totalApplications += $count;
        }
        
        // Sort by count (highest to lowest)
        usort($programData, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // Calculate percentages for bar width
        foreach ($programData as &$program) {
            $program['percentage'] = $totalApplications > 0 
                ? round(($program['count'] / $totalApplications) * 100, 1) 
                : 0;
        }
        
        // Get counts by application type
        $assessmentCount = Application::where('application_type', 'Assessment Only')->count();
        $twspCount = Application::where('application_type', 'TWSP')->count();
        
        return [
            'programs' => $programData,
            'total_applications' => $totalApplications,
            'assessment_count' => $assessmentCount,
            'twsp_count' => $twspCount,
        ];
    }

    private function getTrainingAnalytics()
    {
        // Only count TWSP applications for training analytics
        $completedCount = Application::where('application_type', 'TWSP')
            ->where('training_status', 'completed')
            ->count();
            
        $failedCount = Application::where('application_type', 'TWSP')
            ->where('training_status', 'failed')
            ->count();
        
        // Batch performance - only TWSP applications
        $batches = TrainingBatch::with(['applications' => function($q) {
                $q->where('application_type', 'TWSP');
            }])
            ->whereIn('status', ['active', 'ongoing', 'scheduled', 'completed'])
            ->get()
            ->map(function ($batch) {
                $completed = $batch->applications->where('training_status', 'completed')->count();
                $failed = $batch->applications->where('training_status', 'failed')->count();
                $total = $completed + $failed;
                
                return [
                    'batch_number' => $batch->batch_number,
                    'completed' => $completed,
                    'failed' => $failed,
                    'success_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                ];
            });
        
        // Calculate percentages for pie chart
        $totalAssessed = $completedCount + $failedCount;
        $completedPercentage = $totalAssessed > 0 ? round(($completedCount / $totalAssessed) * 100, 1) : 0;
        $failedPercentage = $totalAssessed > 0 ? round(($failedCount / $totalAssessed) * 100, 1) : 0;
        
        return [
            'completed_count' => $completedCount,
            'failed_count' => $failedCount,
            'batches' => $batches,
            
            // Pie chart data
            'total_assessed' => $totalAssessed,
            'completed_percentage' => $completedPercentage,
            'failed_percentage' => $failedPercentage,
        ];
    }


    private function getAssessmentAnalytics()
    {
        $competentCount = AssessmentResult::where('result', 'Competent')->count();
        $nycCount = AssessmentResult::where('result', 'Not Yet Competent')->count();
        
        // Detailed COC performance by program
        $programs = Application::select('title_of_assessment_applied_for as name')
            ->distinct()
            ->whereHas('assessmentResults')
            ->get()
            ->map(function ($program) {
                $programName = $program->name;
                
                // Get all COC results for this program
                $cocResults = AssessmentCocResult::whereHas('application', function ($q) use ($programName) {
                    $q->where('title_of_assessment_applied_for', $programName);
                })->get();
                
                // Group COC results by COC code
                $cocBreakdown = $cocResults->groupBy('coc_code')->map(function ($cocs, $cocCode) {
                    // ✅ FIX: Count unique applicants, not records
                    $totalApplicants = $cocs->unique('application_id')->count();
                    $competentCocs = $cocs->where('result', 'competent')->count();
                    $nycCocs = $cocs->where('result', 'not_yet_competent')->count();
                    
                    return [
                        'coc_code' => $cocCode,
                        'coc_title' => $cocs->first()->coc_title ?? 'Unknown',
                        'total_assessments' => $totalApplicants,  // ✅ Now shows correct count
                        'competent_count' => $competentCocs,
                        'nyc_count' => $nycCocs,
                        'competent_rate' => $totalApplicants > 0 ? round(($competentCocs / $totalApplicants) * 100, 1) : 0,
                        'nyc_rate' => $totalApplicants > 0 ? round(($nycCocs / $totalApplicants) * 100, 1) : 0,
                    ];
                })->values();
                
                // Overall program statistics
                $totalProgramResults = AssessmentResult::whereHas('application', function ($q) use ($programName) {
                    $q->where('title_of_assessment_applied_for', $programName);
                })->count();
                
                $competentProgramResults = AssessmentResult::where('result', 'Competent')
                    ->whereHas('application', function ($q) use ($programName) {
                        $q->where('title_of_assessment_applied_for', $programName);
                    })->count();
                
                return [
                    'name' => $this->getProgramShortName($programName),
                    'full_name' => $programName,
                    'total_assessments' => $totalProgramResults,
                    'overall_competent_rate' => $totalProgramResults > 0 ? round(($competentProgramResults / $totalProgramResults) * 100, 1) : 0,
                    'overall_nyc_rate' => $totalProgramResults > 0 ? round((($totalProgramResults - $competentProgramResults) / $totalProgramResults) * 100, 1) : 0,
                    'coc_breakdown' => $cocBreakdown,
                ];
            })->filter(function ($program) {
                return $program['total_assessments'] > 0;
            });
        
        // Reassessment analysis
        $firstReassessment = Application::where('reassessment_payment_status', 'verified')->count();
        $secondReassessment = Application::where('second_reassessment_payment_status', 'verified')->count();
        $totalReassessments = $firstReassessment + $secondReassessment;
        
        // Calculate success rate after reassessment
        $successAfterReassessment = AssessmentResult::where('result', 'Competent')
            ->whereHas('application', function ($q) {
                $q->where('reassessment_payment_status', 'verified');
            })->count();
        
        $reassessmentSuccessRate = $totalReassessments > 0 ? round(($successAfterReassessment / $totalReassessments) * 100, 1) : 0;
        
        return [
            'competent_count' => $competentCount,
            'nyc_count' => $nycCount,
            'programs' => $programs,
            'reassessment' => [
                'first' => $firstReassessment,
                'second' => $secondReassessment,
                'success_rate' => $reassessmentSuccessRate,
            ],
        ];
    }

    private function getEmploymentAnalytics()
    {
        // Count TWSP applicants who completed assessment (Competent OR Not Yet Competent)
        // Use application_type instead of scholarship_type for more reliable filtering
        $totalGraduates = Application::where('application_type', 'TWSP')
            ->whereHas('assessmentResults', function ($q) {
                $q->whereIn('result', ['Competent', 'Not Yet Competent']);
            })->count();
        
        $employedCount = \App\Models\EmploymentRecord::count();
        $employmentRate = $totalGraduates > 0 ? round(($employedCount / $totalGraduates) * 100, 1) : 0;
        
        // Average income
        $avgIncome = \App\Models\EmploymentRecord::avg('monthly_income') ?? 0;
        
        // Employment by sector
        $sectors = \App\Models\EmploymentRecord::select('employer_classification as name', DB::raw('count(*) as count'))
            ->groupBy('employer_classification')
            ->get()
            ->map(function ($sector) use ($employedCount) {
                return [
                    'name' => $sector->name,
                    'count' => $sector->count,
                    'percentage' => $employedCount > 0 ? round(($sector->count / $employedCount) * 100, 1) : 0,
                ];
            });
        
        return [
            'employment_rate' => $employmentRate,
            'avg_income' => $avgIncome,
            'sectors' => $sectors,
        ];
    }
    private function getAssessmentVolumeAnalytics($selectedYear = null)
    {
        $currentYear = now()->year;
        $selectedYear = (int) ($selectedYear ?? $currentYear); 
        
     
        $availableYears = range($currentYear, $currentYear + 4);
        
        $monthlyTotals = [];
        for ($month = 1; $month <= 12; $month++) {
            $count = AssessmentResult::whereYear('assessed_at', $selectedYear) 
                ->whereMonth('assessed_at', $month)
                ->whereNotNull('assessed_at')
                ->count();
            
            $monthlyTotals[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'month_short' => date('M', mktime(0, 0, 0, $month, 1)),
                'count' => $count
            ];
        }
        
        // Program breakdown by month
        $programs = [
            'EVENTS MANAGEMENT SERVICES NC III',
            'TOURISM PROMOTION SERVICES NC II',
            'BOOKKEEPING NC III',
            'PHARMACY SERVICES NC III',
            'VISUAL GRAPHIC DESIGN NC III'
        ];
        
        $programMonthlyData = [];
        foreach ($programs as $program) {
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $count = AssessmentResult::whereYear('assessed_at', $selectedYear)  // ✅ Use selected year
                    ->whereMonth('assessed_at', $month)
                    ->whereNotNull('assessed_at') 
                    ->whereHas('application', function($q) use ($program) {
                        $q->where('title_of_assessment_applied_for', $program);
                    })
                    ->count();
                
                $monthlyData[] = $count;
            }
            
            $programMonthlyData[] = [
                'name' => $this->getProgramShortName($program),
                'full_name' => $program,
                'data' => $monthlyData,
                'total' => array_sum($monthlyData)
            ];
        }
        
        // Sort by total (highest first)
        usort($programMonthlyData, function($a, $b) {
            return $b['total'] - $a['total'];
        });
        
        // Yearly totals per program
        $yearlyProgramTotals = [];
        foreach ($programs as $program) {
            $count = AssessmentResult::whereYear('assessed_at', $selectedYear)  // ✅ Use selected year
                ->whereNotNull('assessed_at') 
                ->whereHas('application', function($q) use ($program) {
                    $q->where('title_of_assessment_applied_for', $program);
                })
                ->count();
            
            $yearlyProgramTotals[] = [
                'name' => $this->getProgramShortName($program),
                'full_name' => $program,
                'count' => $count
            ];
        }
        
        // Sort by count (highest first)
        usort($yearlyProgramTotals, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // Calculate percentages for donut chart
        $totalAssessments = array_sum(array_column($yearlyProgramTotals, 'count'));
        foreach ($yearlyProgramTotals as &$program) {
            $program['percentage'] = $totalAssessments > 0 
                ? round(($program['count'] / $totalAssessments) * 100, 1) 
                : 0;
        }
        
        // Overall statistics
        $totalThisYear = AssessmentResult::whereYear('assessed_at', $selectedYear)
            ->whereNotNull('assessed_at')
            ->count();
        $totalLastYear = AssessmentResult::whereYear('assessed_at', $selectedYear - 1)
            ->whereNotNull('assessed_at')
            ->count();
        $yearOverYearGrowth = $totalLastYear > 0 
            ? round((($totalThisYear - $totalLastYear) / $totalLastYear) * 100, 1) 
            : 0;
        
        // Peak month
        $peakMonth = collect($monthlyTotals)->sortByDesc('count')->first();
        
        // Most active program
        $mostActiveProgram = collect($yearlyProgramTotals)->sortByDesc('count')->first();
        
        return [
            'current_year' => $currentYear,
            'selected_year' => $selectedYear,  
            'available_years' => $availableYears,
            'monthly_totals' => $monthlyTotals,
            'program_monthly_data' => $programMonthlyData,
            'yearly_program_totals' => $yearlyProgramTotals,
            'total_this_year' => $totalThisYear,
            'total_last_year' => $totalLastYear,
            'year_over_year_growth' => $yearOverYearGrowth,
            'peak_month' => $peakMonth,
            'most_active_program' => $mostActiveProgram,
            'total_assessments' => $totalAssessments,
        ];
    }

    public function getVolumeAnalyticsData(Request $request)
    {
        $selectedYear = $request->get('year', now()->year);
        $data = $this->getAssessmentVolumeAnalytics($selectedYear);
        
        return response()->json($data);
    }


    private function getCOCCountForProgram($programName)
    {
        // Define COC counts for each program
        $cocCounts = [
            'Bookkeeping NC III' => 3,
            'Visual Graphic Design NC III' => 4,
            'Tourism Promotion Services NC II' => 3,
            'Events Management Services NC III' => 4,
            'Pharmacy Services NC II' => 3,
        ];
        
        // Find matching program (case-insensitive)
        foreach ($cocCounts as $program => $count) {
            if (stripos($programName, $program) !== false || stripos($program, $programName) !== false) {
                return $count;
            }
        }
        
        // Default COC count
        return 3;
    }


    private function getProgramShortName($program)
    {
        // Normalize the program name for comparison (case-insensitive)
        $programLower = strtolower(trim($program));
        
        // Check for bookkeeping variations (with or without NC levels)
        if (str_contains($programLower, 'bookkeeping') || str_contains($programLower, 'book keeping')) {
            return 'BKP';
        }
        
        // Check for other common programs
        if (str_contains($programLower, 'visual graphic design')) {
            return 'VGD';
        }
        
        if (str_contains($programLower, 'tourism promotion')) {
            return 'TPS';
        }
        
        if (str_contains($programLower, 'events management')) {
            return 'EMS';
        }
        
        if (str_contains($programLower, 'pharmacy')) {
            return 'PMS';
        }
        
        // Fallback: take first 3 letters
        return strtoupper(substr($program, 0, 3));
    }
    private function extractBatchNumber($batchName)
    {
        // Handle formats like "BOOK-202603-BATCH-2" or "Batch 2"
        if (preg_match('/BATCH-(\d+)$/i', $batchName, $matches)) {
            return 'B' . $matches[1];
        }
        
        if (preg_match('/Batch\s+(\d+)/i', $batchName, $matches)) {
            return 'B' . $matches[1];
        }
        
        // Fallback: return the original batch name
        return $batchName;
    }


    public function indexApplicants()
    {
        return redirect()->route('admin.applications.index');
    }

    public function storeSchedule(Request $request)
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

        $validated['status'] = TrainingSchedule::STATUS_ACTIVE;

        // Create the schedule
        $schedule = TrainingSchedule::create($validated);

        // Validate end_time is after start_time manually
        if (strtotime($validated['end_time']) <= strtotime($validated['start_time'])) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        // Update training batch status to 'scheduled'
        $batch = TrainingBatch::find($validated['training_batch_id']);
        $batch->update(['status' => TrainingBatch::STATUS_SCHEDULED]);

        // Update all applications in this batch to link with the schedule AND set status to 'ongoing'
        Application::where('training_batch_id', $batch->id)
            ->update([
                'training_schedule_id' => $schedule->id,
                'training_status' => Application::TRAINING_STATUS_ONGOING
            ]);

        return redirect()->route('admin.trainees.index')->with('success', 'Training schedule created successfully for Batch ' . $batch->batch_number);
    }

    //Generate batch name automatically
    private function generateBatchName($ncProgram, $assessmentDate)
    {
    $programCode = strtoupper(substr(str_replace(' ', '', $ncProgram), 0, 4));

    $date = \Carbon\Carbon::parse($assessmentDate);
    $year = $date->format('Y');
    $month = $date->format('m');

    // Get the last batch for this program in the current month
    $lastBatch = AssessmentBatch::where('nc_program', $ncProgram)
        ->whereYear('created_at', $year)
        ->orderBy('id', 'desc')
        ->first();

    $sequence = 1;
    if ($lastBatch && preg_match('/BATCH-(\d+)$/', $lastBatch->batch_name, $matches)) {
        $sequence = intval($matches[1]) + 1;
    }

    return "{$programCode}-{$year}{$month}-BATCH-{$sequence}";
    }

    public function indexApplicationHistory()
    {
        return view('admin.history.index');
    }

    public function archiveEnrollmentSection(Request $request)
    {
    $data = $request->validate([
        'program' => ['required','string','max:255'],
    ]);

    $hasEnrolled = Application::where('status', Application::STATUS_APPROVED)
        ->where('title_of_assessment_applied_for', $data['program'])
        ->where('training_status', Application::TRAINING_STATUS_ENROLLED)
        ->exists();

    if ($hasEnrolled) {
        return back()->with('error', 'There are still enrolled trainees in this section. Complete/Fail all first.');
    }

    EnrollmentArchive::updateOrCreate(
        ['program' => $data['program'], 'schedule_type' => $data['schedule_type']],
        ['archived_by' => $request->user()->id, 'archived_at' => now()]
    );

    return back()->with('success', 'Section archived. Trainees are now available in Training History.');
    }

    public function listApplicationsHistory(Request $request)
    {
        $search = $request->query('q');
        $status = $request->query('status');
        $apps = Application::query()
            ->with('user:id,name') // optional
            ->where('status', '<>', Application::STATUS_PENDING)
            ->when($status, fn($q) => $q->where('status', $status))
             ->when($search, function ($q) use ($search) {
            $q->where(function ($w) use ($search) {
            $w->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
              ->orWhere('title_of_assessment_applied_for', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

            if ($request->ajax()) {
        return view('admin.history.index', compact('apps'))->render();
        }

        return view('admin.history.application.index', compact('apps','status'));
    }

    public function calendar()
    {
        // Fetch calendar data
        $trainingSchedules = TrainingSchedule::with('trainingBatch')
            ->whereIn('status', ['active', 'ongoing', 'scheduled', 'completed'])
            ->get();
        
        $assessmentBatches = AssessmentBatch::whereIn('status', ['scheduled', 'ongoing','completed'])
            ->get();
        
        // Format events for calendar
        $events = [];
        
        // Add training schedules
        foreach ($trainingSchedules as $schedule) {
            $programShort = $this->getProgramShortName($schedule->nc_program);
            $batchShort = 'B' . $schedule->trainingBatch->batch_number;
            
            $events[] = [
            'title' => "{$programShort} ({$batchShort}) - Start",
            'start' => $schedule->start_date->format('Y-m-d'),
            'type' => 'training',
            'color' => '#28a745',
            'extendedProps' => [
                'venue' => $schedule->venue,
                'instructor' => $schedule->instructor,
                'event_type' => 'training_start',
                'start_date' => $schedule->start_date->format('M d, Y'),
                'end_date' => $schedule->end_date->format('M d, Y'),
            ]
        ];
        
        // Training END event (only if different from start date)
        if (!$schedule->start_date->isSameDay($schedule->end_date)) {
            $events[] = [
                'title' => "{$programShort} ({$batchShort}) - End",
                'start' => $schedule->end_date->format('Y-m-d'),
                'type' => 'training',
                'color' => '#74c476',
                'extendedProps' => [
                    'venue' => $schedule->venue,
                    'instructor' => $schedule->instructor,
                    'event_type' => 'training_end',
                    'start_date' => $schedule->start_date->format('M d, Y'),
                    'end_date' => $schedule->end_date->format('M d, Y'),
                ]
            ];
        }
        }
        
        // Add assessment batches
        foreach ($assessmentBatches as $batch) {
            $programShort = $this->getProgramShortName($batch->nc_program);
            $batchName = $this->extractBatchNumber($batch->batch_name);
            
            // Assessment date
            $events[] = [
                'title' => "{$programShort} ({$batchName}) - Assessment",
                'start' => $batch->assessment_date->format('Y-m-d'),
                'type' => 'assessment',
                'color' => '#0d6efd',
                'extendedProps' => [
                    'type' => 'assessment',
                    'venue' => $batch->venue,
                    'assessor' => $batch->assessor_name ?? 'TBA',
                    'time_start' => $batch->start_time ? $batch->start_time->format('h:i A') : 'TBA',
                    'time_end' => $batch->end_time ? $batch->end_time->format('h:i A') : 'TBA',
                ]
            ];
            
            // Intensive Review Day 1
            if ($batch->intensive_review_day1) {
                $events[] = [
                    'title' => "{$programShort} ({$batchName}) - IR Day 1",
                    'start' => $batch->intensive_review_day1->format('Y-m-d'),
                    'type' => 'intensive_review',
                    'color' => '#ffc107',
                    'extendedProps' => [
                        'type' => 'intensive_review',
                        'venue' => $batch->venue,
                        'assessor' => $batch->assessor_name ?? 'TBA',
                        'time_start' => $batch->intensive_review_day1_start ? $batch->intensive_review_day1_start->format('h:i A') : 'TBA',
                        'time_end' => $batch->intensive_review_day1_end ? $batch->intensive_review_day1_end->format('h:i A') : 'TBA',
                    ]
                ];
            }
            
            // Intensive Review Day 2
            if ($batch->intensive_review_day2) {
                $events[] = [
                    'title' => "{$programShort} ({$batchName}) - IR Day 2",
                    'start' => $batch->intensive_review_day2->format('Y-m-d'),
                    'type' => 'intensive_review',
                    'color' => '#ffc107',
                    'extendedProps' => [
                        'type' => 'intensive_review', 
                        'venue' => $batch->venue,
                        'assessor' => $batch->assessor_name ?? 'TBA',
                        'time_start' => $batch->intensive_review_day2_start ? $batch->intensive_review_day2_start->format('h:i A') : 'TBA',
                        'time_end' => $batch->intensive_review_day2_end ? $batch->intensive_review_day2_end->format('h:i A') : 'TBA',
                    ]
                ];
            }
        }
        return view('admin.calendar.index', compact('events'));
    }

}
