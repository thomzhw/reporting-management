<?php
namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\QaTemplate;
use App\Models\QaRule;
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
    

    // Simpan template baru
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'rules' => 'required|array|min:1',
    //         'rules.*.title' => 'required|string|max:255',
    //         'rules.*.description' => 'nullable|string',
    //         'rules.*.requires_photo' => 'sometimes|boolean',
    //         'rules.*.photo_example' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);
    
    //     $template = QaTemplate::create([
    //         'head_id' => Auth::id(),
    //         'name' => $request->name,
    //         'description' => $request->description,
    //     ]);
    
    //     foreach ($request->rules as $index => $rule) {
    //         $ruleData = [
    //             'template_id' => $template->id,
    //             'title' => $rule['title'],
    //             'description' => $rule['description'],
    //             'requires_photo' => $rule['requires_photo'] ?? false,
    //             'order' => $index,
    //         ];
    
    //         // Handle photo example upload
    //         if ($request->hasFile("rules.$index.photo_example")) {
    //             $path = $request->file("rules.$index.photo_example")->store('qa_examples', 'public');
    //             $ruleData['photo_example_path'] = $path;
    //         }
    
    //         QaRule::create($ruleData);
    //     }
    
    //     return redirect()->route('head.qa-templates')->with('success', 'Template created successfully!');
    // }

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
            'rules.*.photo_example' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify that the head manages this outlet
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
        
        foreach ($request->rules as $index => $rule) {
            $ruleData = [
                'template_id' => $template->id,
                'title' => $rule['title'],
                'description' => $rule['description'],
                'requires_photo' => $rule['requires_photo'] ?? false,
                'order' => $index,
            ];
        
            // Handle photo example upload
            if ($request->hasFile("rules.$index.photo_example")) {
                $path = $request->file("rules.$index.photo_example")->store('qa_examples', 'public');
                $ruleData['photo_example_path'] = $path;
            }
        
            QaRule::create($ruleData);
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
            'rules.*.photo_example' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rules.*.remove_photo' => 'sometimes|boolean',
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

        foreach ($request->rules as $index => $rule) {
            $ruleData = [
                'title' => $rule['title'],
                'description' => $rule['description'],
                'requires_photo' => $rule['requires_photo'] ?? false,
                'order' => $index,
            ];

            // Update existing or create new
            if (isset($rule['id'])) {
                $existingRule = QaRule::find($rule['id']);
                
                // Handle photo removal
                if (isset($rule['remove_photo']) && $rule['remove_photo']) {
                    // Hapus file fisik jika ada
                    if ($existingRule->photo_example_path) {
                        Storage::disk('public')->delete($existingRule->photo_example_path);
                    }
                    $ruleData['photo_example_path'] = null;
                }
                
                // Handle photo example upload
                if ($request->hasFile("rules.$index.photo_example")) {
                    // Hapus foto lama jika ada
                    if ($existingRule->photo_example_path) {
                        Storage::disk('public')->delete($existingRule->photo_example_path);
                    }
                    
                    // Simpan foto baru
                    $path = $request->file("rules.$index.photo_example")->store('qa_examples', 'public');
                    $ruleData['photo_example_path'] = $path;
                }
                
                $existingRule->update($ruleData);
            } else {
                // Handle photo example upload untuk rule baru
                if ($request->hasFile("rules.$index.photo_example")) {
                    $path = $request->file("rules.$index.photo_example")->store('qa_examples', 'public');
                    $ruleData['photo_example_path'] = $path;
                }
                
                $ruleData['template_id'] = $template->id;
                QaRule::create($ruleData);
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
