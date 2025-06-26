<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\QaReport;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeadReportController extends Controller
{
    // List reports pending review
    public function index()
    {
        // Get outlets managed by this head
        $managedOutletIds = Auth::user()->managedOutlets()->pluck('outlets.id');
        
        // Get all reports from assignments at those outlets
        $pendingReports = QaReport::whereHas('assignment', function($query) use ($managedOutletIds) {
                $query->whereIn('outlet_id', $managedOutletIds);
            })
            ->where('status', 'pending_review')
            ->with(['staff', 'template', 'assignment.outlet'])
            ->latest('completed_at')
            ->paginate(10);
            
        $approvedReports = QaReport::whereHas('assignment', function($query) use ($managedOutletIds) {
                $query->whereIn('outlet_id', $managedOutletIds);
            })
            ->where('status', 'approved')
            ->with(['staff', 'template', 'assignment.outlet'])
            ->latest('reviewed_at')
            ->paginate(10);
            
        $rejectedReports = QaReport::whereHas('assignment', function($query) use ($managedOutletIds) {
                $query->whereIn('outlet_id', $managedOutletIds);
            })
            ->where('status', 'rejected')
            ->with(['staff', 'template', 'assignment.outlet'])
            ->latest('reviewed_at')
            ->paginate(10);
        
        return view('head.reports.index', compact('pendingReports', 'approvedReports', 'rejectedReports'));
    }
    
    // View report details with review option
    public function show(QaReport $report)
    {
        // Make sure head manages the outlet this report is from
        $outlet = $report->assignment->outlet;
        
        if (!Auth::user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'You are not authorized to review reports for this outlet.');
        }
        
        $report->load(['template.rules', 'responses.rule', 'staff', 'assignment.outlet']);
        
        return view('head.reports.show', compact('report'));
    }
    
    // Process report review (approve/reject)
    public function review(Request $request, QaReport $report)
    {
        // Make sure head manages the outlet this report is from
        $outlet = $report->assignment->outlet;
        
        if (!Auth::user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'You are not authorized to review reports for this outlet.');
        }
        
        // Validate review data
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        // Update report with review details
        $report->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);
        
        // Update assignment status
        if ($request->status === 'approved') {
            $report->assignment->update(['status' => 'completed']);
        } else {
            $report->assignment->update(['status' => 'pending']); // Require resubmission
        }
        
        return redirect()->route('head.reports.index')
            ->with('success', 'Report has been ' . $request->status . ' successfully.');
    }
}