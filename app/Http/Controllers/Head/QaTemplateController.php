<?php
namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\QaTemplate;
use App\Models\QaRule;
use App\Models\QaRulePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QaTemplateController extends Controller
{
    // Daftar template
    public function index()
    {
        $templates = QaTemplate::where('head_id', Auth::id())->withCount('reports')->get();
        return view('head.qa-templates.index', compact('templates'));
    }

    // Form buat template baru
    public function create()
    {
        // Get outlet_id from query parameter if provided
        $outletId = request()->query('outlet_id');
        $outlet = null;
        
        if ($outletId) {
            $outlet = auth()->user()->managedOutlets()->findOrFail($outletId);
        }
        
        // Get all outlets managed by the head for dropdown selection
        $outlets = auth()->user()->managedOutlets;
        
        return view('head.qa-templates.create', compact('outlets', 'outlet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'outlet_id' => 'required|exists:outlets,id',
            'category' => 'nullable|string|max:50',
            'rules' => 'required|array|min:1',
            'rules.*.title' => 'required|string|max:255',
            'rules.*.description' => 'nullable|string',
            'rules.*.requires_photo' => 'sometimes|boolean',
            'rules.*.photos.*' => 'nullable|image|max:2048', // Add validation for images
        ]);

        // Verify outlet management
        if (!auth()->user()->managedOutlets->contains($request->outlet_id)) {
            return back()->withErrors(['outlet_id' => 'You can only create templates for outlets you manage.']);
        }

        $template = QaTemplate::create([
            'head_id' => auth()->id(),
            'outlet_id' => $request->outlet_id,
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
        ]);
        
        foreach ($request->rules as $index => $ruleData) {
            $rule = QaRule::create([
                'template_id' => $template->id,
                'title' => $ruleData['title'],
                'description' => $ruleData['description'] ?? null,
                'requires_photo' => isset($ruleData['requires_photo']) && $ruleData['requires_photo'] == '1',
                'order' => $index,
            ]);
            
            // Handle file uploads
            try {
                if ($request->hasFile("rules.$index.photos")) {
                    $photos = $request->file("rules.$index.photos");
                    
                    foreach ($photos as $photoIndex => $photo) {
                        $path = $photo->store('qa_examples', 'public');
                        
                        QaRulePhoto::create([
                            'rule_id' => $rule->id,
                            'photo_path' => $path,
                            'order' => $photoIndex
                        ]);
                        
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error uploading photos:', [
                    'rule_id' => $rule->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        return redirect()->route('head.qa-templates')->with('success', 'Template created successfully!');
    }

    // Edit template
    public function edit(QaTemplate $template)
    {
        $this->authorize('update', $template);
    
        // Get all outlets managed by the head for dropdown selection
        $outlets = auth()->user()->managedOutlets;
        
        $template->load('rules');
        return view('head.qa-templates.edit', compact('template', 'outlets'));
    }

    public function update(Request $request, QaTemplate $template)
    {
        $this->authorize('update', $template);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'outlet_id' => 'required|exists:outlets,id',
            'category' => 'nullable|string|max:50',
            'rules' => 'required|array|min:1',
            'rules.*.title' => 'required|string|max:255',
            'rules.*.description' => 'nullable|string',
            'rules.*.requires_photo' => 'sometimes|boolean',
            'rules.*.photos' => 'nullable|array',
            'rules.*.photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rules.*.remove_photos' => 'nullable|array',
            'rules.*.remove_photos.*' => 'nullable|integer|exists:qa_rule_photos,id',
        ]);

        // Verify that the head manages this outlet
        if (!auth()->user()->managedOutlets->contains($request->outlet_id)) {
            return back()->withErrors(['outlet_id' => 'You can only assign templates to outlets you manage.']);
        }

        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'outlet_id' => $request->outlet_id,
            'category' => $request->category,
        ]);

        foreach ($request->rules as $index => $ruleData) {
            // Update or create rule
            if (isset($ruleData['id'])) {
                $rule = QaRule::find($ruleData['id']);
                $rule->update([
                    'title' => $ruleData['title'],
                    'description' => $ruleData['description'] ?? null,
                    'requires_photo' => $ruleData['requires_photo'] ?? false,
                    'order' => $index,
                ]);
            } else {
                $rule = QaRule::create([
                    'template_id' => $template->id,
                    'title' => $ruleData['title'],
                    'description' => $ruleData['description'] ?? null,
                    'requires_photo' => $ruleData['requires_photo'] ?? false,
                    'order' => $index,
                ]);
            }
            
            // Handle photo removals
            if (isset($ruleData['remove_photos']) && is_array($ruleData['remove_photos'])) {
                foreach ($ruleData['remove_photos'] as $photoId) {
                    $photo = QaRulePhoto::find($photoId);
                    if ($photo && $photo->rule_id == $rule->id) {
                        // Delete the file
                        Storage::disk('public')->delete($photo->photo_path);
                        // Delete the record
                        $photo->delete();
                    }
                }
            }

            // Handle new photo uploads
            if (isset($ruleData['photos']) && is_array($ruleData['photos'])) {
                foreach ($ruleData['photos'] as $photoIndex => $photo) {
                    if ($request->hasFile("rules.$index.photos.$photoIndex")) {
                        $path = $request->file("rules.$index.photos.$photoIndex")->store('qa_examples', 'public');
                        
                        QaRulePhoto::create([
                            'rule_id' => $rule->id,
                            'photo_path' => $path,
                            'caption' => $ruleData['photo_captions'][$photoIndex] ?? null,
                            'order' => $rule->photos()->count() // Add at the end
                        ]);
                    }
                }
            }
        }

        return redirect()->route('head.qa-templates')->with('success', 'Template updated successfully!');
    }

    // Hapus template
    public function destroy(QaTemplate $template)
    {
        $this->authorize('delete', $template);
        $template->delete();
        return back()->with('success', 'Template deleted successfully!');
    }
}
