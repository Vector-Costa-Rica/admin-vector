<?php

namespace App\Http\Controllers;

use App\Models\Client_doc;
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

class ClientDocsController extends Controller
{
    protected Client_doc $client_doc;

    public function __construct(Client_doc $client_doc)
    {
        $this->client_doc = $client_doc;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $client_docs = Client_doc::query()->latest()->paginate(10);
        return view('admin.client_docs.index')->with(['client_docs' => $client_docs, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('client_docs.create');
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
            $file->storeAs('client_docs', $filename, 'public');

            $client_doc = new Client_doc();
            $client_doc->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $client_doc->save();

            return redirect()->route('client_docs.index')->with('success', 'Client doc added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $client_doc = $this->client_doc->getClient_docById($id);
        return view('client_docs.show', ['client_docs' => $client_doc]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client_doc $client_doc): Response|Factory|Application|View
    {
        return view('modals.edit_delete_client_docs')->with(['client_doc' => $client_doc]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client_doc $client_doc): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $client_doc->getAttribute('file');
            Storage::disk('public')->delete('client_docs/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('client_docs', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $client_doc->forceFill($updateData);
        $client_doc->save();

        return redirect()->route('client_docs.index')->with('success', 'Client doc updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client_doc $client_doc): Response|RedirectResponse
    {
        $file = $client_doc->getAttribute('file');
        Storage::disk('public')->delete('client_docs/' . $file);

        $client_doc->delete();
        return redirect()->route('client_docs.index')->with('success', 'Client doc deleted successfully');
    }

    public function download(Client_doc $client_doc): StreamedResponse|RedirectResponse
    {

        try {

            $file = $client_doc->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this client doc");
            }

            if (!Storage::disk('public')->exists('client_docs/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($client_doc->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('client_docs/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
