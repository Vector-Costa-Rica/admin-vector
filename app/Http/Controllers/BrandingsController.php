<?php

namespace App\Http\Controllers;

use App\Models\Branding;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BrandingsController extends Controller
{

    protected Branding $branding;

    public function __construct(Branding $branding)
    {
        $this->branding = $branding;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $brandings = Branding::query()->latest()->paginate(10);
        return view('admin.brandings.index')->with(['brandings' => $brandings, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('brandings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'required|file'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('brandings', $filename, 'public');

            $branding = new Branding();
            $branding->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $branding->save();

            return redirect()->route('brandings.index')->with('success', 'Branding added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View|Factory|Application
    {
        $branding = $this->branding->getBrandingById($id);
        return view('brandings.show', ['brandings' => $branding]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branding $branding): Response|Factory|Application|View
    {
        return view('modals.edit_delete_brandings')->with('branding', $branding);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branding $branding): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $branding->getAttribute('file');
            Storage::disk('public')->delete('brandings/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('brandings', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $branding->forceFill($updateData);
        $branding->save();

        return redirect()->route('brandings.index')->with('success', 'Branding updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branding $branding): Response|RedirectResponse
    {
        $file = $branding->getAttribute('file');
        Storage::disk('public')->delete('brandings/' . $file);

        $branding->delete();
        return redirect()->route('brandings.index')->with('success', 'Branding deleted successfully');
    }

    public function download(Branding $branding): StreamedResponse|RedirectResponse
    {

        try {

            $file = $branding->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this branding");
            }

            if (!Storage::disk('public')->exists('brandings/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($branding->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('brandings/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
