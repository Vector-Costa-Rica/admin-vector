<?php

namespace App\Http\Controllers;

use App\Models\Tech_doc;
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

class TechDocsController extends Controller
{
    protected Tech_doc $tech_doc;

    public function __construct(Tech_doc $tech_doc)
    {
        $this->tech_doc = $tech_doc;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $tech_docs = Tech_doc::query()->latest()->paginate(10);
        return view('admin.tech_docs.index')->with(['tech_docs' => $tech_docs, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('tech_docs.create');
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
            $file->storeAs('tech_docs', $filename, 'public');

            $tech_doc = new Tech_doc();
            $tech_doc->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $tech_doc->save();

            return redirect()->route('tech_docs.index')->with('success', 'Tech doc added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $tech_doc = $this->tech_doc->getTech_docById($id);
        return view('tech_docs.show', ['tech_docs' => $tech_doc]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tech_doc $tech_doc): Response|Factory|Application|View
    {
        return view('modals.edit_delete_tech_docs')->with(['tech_doc' => $tech_doc]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tech_doc $tech_doc): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $tech_doc->getAttribute('file');
            Storage::disk('public')->delete('tech_docs/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('tech_docs', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $tech_doc->forceFill($updateData);
        $tech_doc->save();

        return redirect()->route('tech_docs.index')->with('success', 'Tech doc updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tech_doc $tech_doc): Response|RedirectResponse
    {
        $file = $tech_doc->getAttribute('file');
        Storage::disk('public')->delete('tech_docs/' . $file);

        $tech_doc->delete();
        return redirect()->route('tech_docs.index')->with('success', 'Tech doc deleted successfully');
    }

    public function download(Tech_doc $tech_doc): StreamedResponse|RedirectResponse
    {

        try {

            $file = $tech_doc->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this tech doc");
            }

            if (!Storage::disk('public')->exists('tech_docs/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($tech_doc->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('tech_docs/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
