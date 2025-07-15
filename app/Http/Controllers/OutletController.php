<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\QaTemplateAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    /**
     * Display a listing of the outlets.
     */
    public function index()
    {
        $outlets = Outlet::withCount(['heads', 'staffs'])->get();
        return view('superuser.outlets.index', compact('outlets'));
    }

    /**
     * Show the form for creating a new outlet.
     */
    public function create()
    {
        return view('superuser.outlets.create');
    }

    /**
     * Store a newly created outlet in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'type' => 'required|string',
            'description' => 'nullable|string',
        ]);
        
        Outlet::create($request->all());
        
        return redirect()->route('outlets.index')
            ->with('success', 'Outlet created successfully.');
    }

    /**
     * Display the specified outlet.
     */
    public function show(Outlet $outlet)
    {
        $outlet->load(['heads', 'staffs']);
        return view('superuser.outlets.show', compact('outlet'));
    }

    /**
     * Show the form for editing the specified outlet.
     */
    public function edit(Outlet $outlet)
    {
        return view('superuser.outlets.edit', compact('outlet'));
    }

    /**
     * Update the specified outlet in storage.
     */
    public function update(Request $request, Outlet $outlet)
    {
        $isChangingStatus = $request->has('status') && $request->status !== $outlet->status;
        
        // If changing status, check if outlet has staff or head
        if ($isChangingStatus && ($outlet->heads()->count() > 0 || $outlet->staffs()->count() > 0)) {
            return redirect()->route('outlets.index')
                ->with('error', 'Cannot change outlet status because it has associated staff or head.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $outlet->update($request->all());
        
        return redirect()->route('outlets.index')
            ->with('success', 'Outlet updated successfully.');
    }

    /**
     * Remove the specified outlet from storage.
     */
    public function destroy(Outlet $outlet)
    {
        // Begin transaction to ensure database consistency
    \DB::beginTransaction();
    
    try {
        if($outlet->staffs()->exists()) {
            return back()->withErrors([
                'outlet' => 'Cannot delete: Outlet has Staff!'
            ]);
        }
        
        // Detach all heads from this outlet (removes relationships, not the users)
        $outlet->heads()->detach();
        
        // Detach all staff from this outlet
        $outlet->staffs()->detach();
        
        $outlet->qaTemplates()->delete(); // This would cascade delete assignments and reports
        
        // Finally, delete the outlet
        $outlet->delete();
        
        // Commit the transaction
        \DB::commit();
        
        return redirect()->route('outlets.index')
            ->with('success', 'Outlet deleted successfully and all related assignments have been updated.');
    } catch (\Exception $e) {
        // If anything goes wrong, rollback the transaction
        \DB::rollBack();
        
        return redirect()->route('outlets.index')
            ->with('error', 'Failed to delete outlet: ' . $e->getMessage());
    }
    }
    
    /**
     * Show the form for assigning heads to an outlet.
     */
    public function assignHeads(Outlet $outlet)
    {
        $heads = User::whereHas('role', function($q) {
            $q->where('name', 'timhub');
        })->get();
        
        $assignedHeads = $outlet->heads;
        
        return view('superuser.outlets.assign-heads', compact('outlet', 'heads', 'assignedHeads'));
    }
    
    /**
     * Update the head assignments for an outlet.
     */
    public function updateHeads(Request $request, Outlet $outlet)
    {
        $request->validate([
            'head_ids' => 'required|array',
            'head_ids.*' => 'exists:users,id'
        ]);
        
        $outlet->heads()->sync($request->head_ids);
        
        return redirect()->route('outlets.show', $outlet)
            ->with('success', 'Timhubs assigned to remote successfully.');
    }
}