<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\QaTemplateAssignment;
use App\Models\QaReport;
use App\Models\User;

class StaffController extends Controller
{
    /**
     * Dashboard for staff
     */
    public function dashboard()
    {
        $user = Auth::user();
    
        // Get outlets assigned to this staff
        $assignedOutlets = $user->assignedOutlets;
        
        // Get pending assignments
        $pendingAssignments = QaTemplateAssignment::where('staff_id', $user->id)
            ->where('status', 'pending')
            ->whereDoesntHave('report')
            ->with(['template', 'outlet'])
            ->get();
        
        // Get overdue assignments
        $overdueAssignments = QaTemplateAssignment::where('staff_id', $user->id)
            ->where('status', 'pending')
            ->whereDoesntHave('report')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->with(['template', 'outlet'])
            ->get();
        
        // Get completed reports
        $completedReports = QaReport::where('staff_id', $user->id)
            ->with(['template', 'assignment.outlet'])
            ->latest('completed_at')
            ->take(5)
            ->get();
        
        return view('staff.dashboard', compact(
            'user', 
            'assignedOutlets', 
            'pendingAssignments', 
            'overdueAssignments',
            'completedReports'
        ));
    }

    /**
     * Profile page for staff
     */
    public function myProfile()
    {
        $user = Auth::user();
        return view('staff.profile', compact('user'));
    }
}