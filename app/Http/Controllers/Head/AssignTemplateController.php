<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\QaTemplate;
use App\Models\QaTemplateAssignment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignTemplateController extends Controller
{
    public function index()
    {
        // Get outlet filter from query string if provided
        $outletId = request()->query('outlet_id');
        
        // Base query for assignments made by this head
        $query = QaTemplateAssignment::where('assigned_by', Auth::id())
            ->with(['template', 'staff', 'outlet', 'report']);
        
        // Apply outlet filter if provided
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
            
        $assignments = $query->latest()->paginate(10);
        
        // Get outlets managed by this head for the filter dropdown
        $outlets = Auth::user()->managedOutlets;
            
        return view('head.assignments.index', compact('assignments', 'outlets', 'outletId'));
    }
    
    public function create()
    {
        // Get pre-selected outlet_id from query string if provided
        $selectedOutletId = request()->query('outlet_id');
        $selectedStaffId = request()->query('staff_id');
        $selectedTemplateId = request()->query('template_id');
        
        // Get outlets managed by this head
        $outlets = Auth::user()->managedOutlets;
        
        // Pass minimal data initially - we'll load staff and templates via AJAX based on selected outlet
        return view('head.assignments.create', compact(
            'outlets', 
            'selectedOutletId',
            'selectedStaffId',
            'selectedTemplateId'
        ));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'template_id' => 'required|exists:qa_templates,id',
            'staff_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
            // 'assignment_reference' => 'nullable|string|max:100',
        ]);
        
        // Verify head manages this outlet
        if (!Auth::user()->managedOutlets->contains($request->outlet_id)) {
            return back()->withErrors(['outlet_id' => 'You can only assign templates for outlets you manage']);
        }
        
        // Verify staff is assigned to this outlet
        $outlet = Auth::user()->managedOutlets()->findOrFail($request->outlet_id);
        if (!$outlet->staffs->contains($request->staff_id)) {
            return back()->withErrors(['staff_id' => 'This staff is not assigned to the selected outlet']);
        }
        
        // Verify template belongs to this outlet
        $template = QaTemplate::findOrFail($request->template_id);
        if ($template->outlet_id != $request->outlet_id) {
            return back()->withErrors(['template_id' => 'This template does not belong to the selected outlet']);
        }

        // Check if a similar active assignment already exists
        $existingAssignment = QaTemplateAssignment::where([
            'template_id' => $request->template_id,
            'staff_id' => $request->staff_id,
            'status' => 'pending'
        ])->first();

        if ($existingAssignment) {
            $staffName = User::find($request->staff_id)->name;
            $templateName = $template->name;
            return back()->withErrors([
                'duplicate' => "This staff member ({$staffName}) already has a pending assignment for this template ({$templateName}). Please wait until the current assignment is completed before assigning again."
            ])->withInput();
        }
        
        try {
            QaTemplateAssignment::create([
                'template_id' => $request->template_id,
                'staff_id' => $request->staff_id,
                'outlet_id' => $request->outlet_id,
                'assigned_by' => Auth::id(),
                'due_date' => $request->due_date,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);
            
            return redirect()->route('head.assignments.index')
                ->with('success', 'Template assigned successfully to staff member!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                $staffName = User::find($request->staff_id)->name;
                $templateName = $template->name;
                return back()->withErrors([
                    'duplicate' => "This staff member ({$staffName}) already has a pending assignment for this template ({$templateName}). Please wait until the current assignment is completed before assigning again."
                ])->withInput();
            }
            
            // For other database errors
            return back()->withErrors(['error' => 'Database error: ' . $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            // For any other unexpected errors
            return back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()])->withInput();
        }
    }
    
    public function show(QaTemplateAssignment $assignment)
    {
        // Ensure this assignment was created by the authenticated head
        if ($assignment->assigned_by != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $assignment->load(['template.rules', 'staff', 'outlet', 'report.responses.rule', 'report.responses.photos']);

        
        return view('head.assignments.show', compact('assignment'));
    }
    
    public function destroy(QaTemplateAssignment $assignment)
    {
        // Ensure this assignment was created by the authenticated head
        if ($assignment->assigned_by != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Only allow deletion if report hasn't been started
        if ($assignment->report) {
            return back()->withErrors(['assignment' => 'Cannot delete: Report already exists']);
        }
        
        $assignment->delete();
        return back()->with('success', 'Assignment removed successfully');
    }

    // AJAX endpoint to get staff and templates for a selected outlet
    public function getOutletData(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
        ]);
        
        $outlet = Auth::user()->managedOutlets()->findOrFail($request->outlet_id);
        
        // Get staff assigned to this outlet
        $staff = $outlet->staffs;
        
        // Get templates created for this outlet
        $templates = QaTemplate::where('outlet_id', $outlet->id)
            ->where('head_id', Auth::id())
            ->get();
            
        return response()->json([
            'staff' => $staff,
            'templates' => $templates
        ]);
    }

    public function exportPdf(QaTemplateAssignment $assignment)
    {
        // Ensure this assignment was created by the authenticated head
        if ($assignment->assigned_by != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Load all the necessary relationships including photos
        $assignment->load(['template.rules', 'staff', 'outlet', 'report.responses.rule', 'report.responses.photos']);
        
        // Generate PDF using a PDF view
        $pdf = Pdf::loadView('head.assignments.pdf', compact('assignment'));
        
        // Download the PDF with a custom filename
        return $pdf->download('Assignment-' . $assignment->assignment_reference . '.pdf');
    }
}