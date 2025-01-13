<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
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

class ProposalsController extends Controller
{
    protected Proposal $proposal;

    public function __construct(Proposal $proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $proposals = Proposal::query()->latest()->paginate(10);
        return view('admin.proposals.index')->with(['proposals' => $proposals, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('proposals.create');
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
            $file->storeAs('proposals', $filename, 'public');

            $proposal = new Proposal();
            $proposal->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $proposal->save();

            return redirect()->route('proposals.index')->with('success', 'Proposal added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $proposal = $this->proposal->getProposalById($id);
        return view('proposals.show', ['proposals' => $proposal]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposal $proposal): Response|Factory|Application|View
    {
        return view('modals.edit_delete_proposals')->with(['proposal' => $proposal]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proposal $proposal): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $proposal->getAttribute('file');
            Storage::disk('public')->delete('proposals/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('proposals', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $proposal->forceFill($updateData);
        $proposal->save();

        return redirect()->route('proposals.index')->with('success', 'Proposal updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal): Response|RedirectResponse
    {
        $file = $proposal->getAttribute('file');
        Storage::disk('public')->delete('proposals/' . $file);

        $proposal->delete();
        return redirect()->route('proposals.index')->with('success', 'Proposal deleted successfully');
    }

    public function download(Proposal $proposal): StreamedResponse|RedirectResponse
    {

        try {

            $file = $proposal->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this proposal");
            }

            if (!Storage::disk('public')->exists('proposals/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($proposal->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('proposals/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
