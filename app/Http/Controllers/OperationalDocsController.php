<?php

namespace App\Http\Controllers;

use App\Models\Operational_doc;
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

class OperationalDocsController extends Controller
{
    protected Operational_doc $operational_doc;

    public function __construct(Operational_doc $operational_doc)
    {
        $this->operational_doc = $operational_doc;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $operational_docs = Operational_doc::query()->latest()->paginate(10);
        return view('admin.operational_docs.index')
            ->with(['operational_docs' => $operational_docs, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('operational_docs.create');
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
            $file->storeAs('operational_docs', $filename, 'public');

            $operational_doc = new Operational_doc();
            $operational_doc->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $operational_doc->save();

            return redirect()->route('operational_docs.index')->with('success', 'Operational doc added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $operational_doc = $this->operational_doc->getOperational_docById($id);
        return view('operational_docs.show', ['operational_docs' => $operational_doc]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operational_doc $operational_doc): Response|Factory|Application|View
    {
        return view('modals.edit_delete_operational_docs')->with(['operational_doc' => $operational_doc]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Operational_doc $operational_doc): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $operational_doc->getAttribute('file');
            Storage::disk('public')->delete('operational_docs/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('operational_docs', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $operational_doc->forceFill($updateData);
        $operational_doc->save();

        return redirect()->route('operational_docs.index')->with('success', 'Operational doc updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operational_doc $operational_doc): Response|RedirectResponse
    {
        $file = $operational_doc->getAttribute('file');
        Storage::disk('public')->delete('operational_docs/' . $file);

        $operational_doc->delete();
        return redirect()->route('operational_docs.index')->with('success', 'Operational doc deleted successfully');
    }

    public function download(Operational_doc $operational_doc): StreamedResponse|RedirectResponse
    {

        try {

            $file = $operational_doc->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this operational doc");
            }

            if (!Storage::disk('public')->exists('operational_docs/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($operational_doc->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('operational_docs/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
