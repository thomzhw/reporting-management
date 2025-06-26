<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeadController extends Controller
{
    /**
     * Dashboard khusus head
     */
    public function dashboard()
    {
        return view('head.dashboard');
    }

    // Add to HeadController.php

    public function viewOutlets()
    {
        $outlets = auth()->user()->managedOutlets;
        return view('head.outlets.index', compact('outlets'));
    }

    public function outletStaff(Outlet $outlet)
    {
        // Verify the authenticated head manages this outlet
        if (!auth()->user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'Unauthorized');
        }
        
        $staff = User::whereHas('role', function($q) {
            $q->where('name', 'staff');
        })->get();
        
        $assignedStaff = $outlet->staffs;
        
        return view('head.outlets.staff', compact('outlet', 'staff', 'assignedStaff'));
    }

    public function assignStaff(Request $request, Outlet $outlet)
    {
        // Verify the authenticated head manages this outlet
        if (!auth()->user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'staff_assignments' => 'required|array',
            'staff_assignments.*.staff_id' => 'required|exists:users,id',
            'staff_assignments.*.role' => 'required|string'
        ]);
        
        $staffData = [];
        foreach ($request->staff_assignments as $assignment) {
            $staffData[$assignment['staff_id']] = [
                'role' => $assignment['role'],
                'assigned_at' => now(),
                'assigned_by' => auth()->id()
            ];
        }
        
        $outlet->staffs()->sync($staffData);
        
        return redirect()->route('head.outlets.index')
            ->with('success', 'Staff assigned to outlet successfully.');
    }

    public function createQaTemplate(Outlet $outlet)
    {
        // Verify the authenticated head manages this outlet
        if (!auth()->user()->managedOutlets->contains($outlet->id)) {
            abort(403, 'Unauthorized');
        }
        
        return view('head.qa-templates.create', compact('outlet'));
    }

}