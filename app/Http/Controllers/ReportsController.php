<?php

namespace App\Http\Controllers;

use App\Models\Report;
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

class ReportsController extends Controller
{
    protected Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $reports = Report::query()->latest()->paginate(10);
        return view('admin.reports.index')->with(['reports' => $reports, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('reports.create');
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
            $file->storeAs('reports', $filename, 'public');

            $report = new Report();
            $report->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $report->save();

            return redirect()->route('reports.index')->with('success', 'Report added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $report = $this->report->getReportById($id);
        return view('reports.show', ['reports' => $report]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report): Response|Factory|Application|View
    {
        return view('modals.edit_delete_reports')->with(['report' => $report]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $report->getAttribute('file');
            Storage::disk('public')->delete('reports/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('reports', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $report->forceFill($updateData);
        $report->save();

        return redirect()->route('reports.index')->with('success', 'Report updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report): Response|RedirectResponse
    {
        $file = $report->getAttribute('file');
        Storage::disk('public')->delete('reports/' . $file);

        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report deleted successfully');
    }

    public function download(Report $report): StreamedResponse|RedirectResponse
    {

        try {

            $file = $report->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this report");
            }

            if (!Storage::disk('public')->exists('reports/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($report->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('reports/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
