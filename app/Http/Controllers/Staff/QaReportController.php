<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\QaTemplate;
use App\Models\QaReport;
use App\Models\QaReportResponse;
use App\Models\QaReportResponsePhoto;
use App\Models\QaTemplateAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QaReportController extends Controller
{
    // Display list of all assignments for the staff
    public function index()
    {
        $assignments = QaTemplateAssignment::where('staff_id', Auth::id())
            ->with(['template', 'outlet', 'report'])
            ->latest()
            ->paginate(10);
            
        return view('staff.qa-reports.index', compact('assignments'));
    }

    // Create a report from an assignment
    public function create(QaTemplateAssignment $assignment)
    {
        // Ensure this assignment is for the authenticated staff
        if ($assignment->staff_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Check if there's a report
        if ($assignment->report) {
            // If it's approved, don't allow redoing
            if ($assignment->report->status == 'approved') {
                return redirect()->route('staff.qa-reports.show', $assignment->report)
                    ->with('info', 'This report has already been approved and cannot be redone.');
            }
            
            // If it's pending review, don't allow redoing yet
            if ($assignment->report->status == 'pending_review') {
                return redirect()->route('staff.qa-reports.show', $assignment->report)
                    ->with('info', 'This report is still pending review by your head.');
            }
            
            // For rejected reports, allow redoing
            // Set the old report to 'replaced' or similar status if you want to track history
        }
        
        // Load template with rules
        $assignment->load('template.rules', 'outlet');
        
        return view('staff.qa-reports.create', compact('assignment'));
    }

    public function store(Request $request, QaTemplateAssignment $assignment)
    {
        // Ensure this assignment is for the authenticated staff
        if ($assignment->staff_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Check if there's an existing report
        $existingReport = $assignment->report;
        
        // If there's a report and it's not rejected, don't allow updating it
        if ($existingReport && $existingReport->status != 'rejected') {
            return redirect()->route('staff.qa-reports.show', $existingReport)
                ->with('info', 'This assignment already has a report that is not rejected.');
        }
        
        // Validate responses
        $rules = [];

        // Build dynamic validation rules
        foreach ($assignment->template->rules as $rule) {
            $rules["responses.{$rule->id}.response"] = 'required|string';
            
            if ($rule->requires_photo) {
                $rules["responses.{$rule->id}.photos"] = 'required|array|min:1';
                $rules["responses.{$rule->id}.photos.*"] = 'required|image|max:5120'; // 5MB limit
            } else {
                $rules["responses.{$rule->id}.photos"] = 'nullable|array';
                $rules["responses.{$rule->id}.photos.*"] = 'nullable|image|max:5120'; // 5MB limit
            }
        }
        
        $request->validate($rules);
        
        // Determine if this is a new report or an update to a rejected report
        $isResubmission = $existingReport && $existingReport->status == 'rejected';
        
        if ($isResubmission) {
            // Update existing report
            $report = $existingReport;
            $report->update([
                'status' => 'pending_review',
                'completed_at' => now(),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'feedback' => null, // Clear previous feedback
            ]);
            
            // Delete old responses and their photos
            foreach ($report->responses as $response) {
                // Delete the old single photo if it exists
                if ($response->photo_path && Storage::disk('public')->exists($response->photo_path)) {
                    Storage::disk('public')->delete($response->photo_path);
                }
                
                // Delete all photos in the new photos table
                foreach ($response->photos as $photo) {
                    if (Storage::disk('public')->exists($photo->photo_path)) {
                        Storage::disk('public')->delete($photo->photo_path);
                    }
                    $photo->delete();
                }
            }
            
            // Now delete the responses
            $report->responses()->delete();
        } else {
            // Create a new report
            $report = QaReport::create([
                'template_id' => $assignment->template_id,
                'staff_id' => Auth::id(),
                'assignment_id' => $assignment->id,
                'completed_at' => now(),
                'status' => 'pending_review',
            ]);
        }
        
        // Create new responses for each rule
        foreach ($assignment->template->rules as $rule) {
            $response = QaReportResponse::create([
                'report_id' => $report->id,
                'rule_id' => $rule->id,
                'response' => $request->input("responses.{$rule->id}.response"),
            ]);
            
            // Handle photo uploads
            if ($request->hasFile("responses.{$rule->id}.photos")) {
                $photos = $request->file("responses.{$rule->id}.photos");
                
                foreach ($photos as $photoIndex => $photo) {
                    if ($photo->isValid()) {
                        $path = $photo->store('qa_evidence', 'public');
                        
                        // For backward compatibility, if this is the first photo, also store it in the response record
                        if ($photoIndex === 0) {
                            $response->update(['photo_path' => $path]);
                        }
                        
                        // Create the response photo record
                        QaReportResponsePhoto::create([
                            'response_id' => $response->id,
                            'photo_path' => $path,
                            'order' => $photoIndex
                        ]);
                    }
                }
            }
        }
        
        // Update assignment status
        $assignment->update(['status' => 'in_progress']);
        
        return redirect()->route('staff.qa-reports.show', $report)
            ->with('success', $isResubmission ? 'Report resubmitted successfully and is pending review!' : 'Report submitted successfully and is pending review!');
    }

    // View a completed report
    public function show(QaReport $report)
    {
        // Ensure only the staff who created the report can view it
        if ($report->staff_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $completedReports = QaReport::where('staff_id', Auth::id())
        ->with(['template', 'assignment.outlet'])
        ->latest('completed_at')
        ->take(5)
        ->get();
        
        $report->load('template.rules', 'responses.rule', 'assignment.outlet');
        
        return view('staff.qa-reports.show', compact('report', 'completedReports'));
    }
}