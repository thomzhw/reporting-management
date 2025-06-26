<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeadOutletController extends Controller
{
    /**
     * Display outlets managed by the authenticated head.
     */
    public function index()
    {
        $outlets = Auth::user()->managedOutlets()->withCount('staffs')->get();
        return view('head.outlets.index', compact('outlets'));
    }
    
    /**
     * Display the specified outlet.
     */
    public function show(Outlet $outlet)
    {
        // Verify the head manages this outlet
        if (!Auth::user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'Unauthorized action.');
        }
        
        $outlet->load(['staffs' => function($query) {
            $query->orderBy('name');
        }]);
        
        return view('head.outlets.show', compact('outlet'));
    }
    
    /**
     * Show the form for assigning staff to an outlet.
     */
    public function assignStaff(Outlet $outlet)
    {
        // Verify the head manages this outlet
        if (!Auth::user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get all staff users
        $availableStaff = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->orderBy('name')->get();
        
        // Get staff already assigned to this outlet
        $assignedStaff = $outlet->staffs;
        
        return view('head.outlets.assign-staff', compact('outlet', 'availableStaff', 'assignedStaff'));
    }
    
    /**
     * Update staff assignments for an outlet.
     */

    public function updateStaffAssignments(Request $request, Outlet $outlet)
    {
        if (!Auth::user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'staff_assignments' => 'array',
            'staff_assignments.*.role' => 'sometimes|nullable|string|max:255',
            'staff_assignments.*.assign' => 'sometimes|boolean',
        ]);

        $staffData = [];
        
        // Loop through all submitted staff assignments
        foreach ($request->staff_assignments ?? [] as $staffId => $assignment) {
            
            // Only add to sync if staff is assigned
            if (isset($assignment['assign']) && $assignment['assign'] == '1') {
                // Use a default role if none provided
                $role = !empty($assignment['role']) ? $assignment['role'] : 'Staff';
                
                $staffData[$staffId] = [
                    'role' => $role,
                    'assigned_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Sync will remove unassigned staff and add/update assigned staff
        $result = $outlet->staffs()->sync($staffData);
        
        return redirect()->route('head.outlets.show', $outlet)
            ->with('success', 'Staff assignments updated successfully.');
    }

}