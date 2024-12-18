<?php

namespace App\Http\Controllers;

use App\Models\Custom_asset;
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

class CustomAssetsController extends Controller
{
    protected Custom_asset $custom_asset;

    public function __construct(Custom_asset $custom_asset)
    {
        $this->custom_asset = $custom_asset;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $custom_assets = Custom_asset::query()->latest()->paginate(10);
        return view('admin.custom_assets.index')
            ->with(['custom_assets' => $custom_assets, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('custom_assets.create');
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
            $file->storeAs('custom_assets', $filename, 'public');

            $custom_asset = new Custom_asset();
            $custom_asset->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $custom_asset->save();

            return redirect()->route('custom_assets.index')->with('success', 'Custom asset added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $custom_asset = $this->custom_asset->getCustom_assetById($id);
        return view('custom_assets.show', ['custom_assets' => $custom_asset]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Custom_asset $custom_asset): Response|Factory|Application|View
    {
        return view('modals.edit_delete_custom_assets')->with(['custom_asset' => $custom_asset]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Custom_asset $custom_asset): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $custom_asset->getAttribute('file');
            Storage::disk('public')->delete('custom_assets/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('custom_assets', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $custom_asset->forceFill($updateData);
        $custom_asset->save();

        return redirect()->route('custom_assets.index')->with('success', 'Custom asset updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Custom_asset $custom_asset): Response|RedirectResponse
    {
        $file = $custom_asset->getAttribute('file');
        Storage::disk('public')->delete('custom_assets/' . $file);

        $custom_asset->delete();
        return redirect()->route('custom_assets.index')->with('success', 'Custom asset deleted successfully');
    }

    public function download(Custom_asset $custom_asset): StreamedResponse|RedirectResponse
    {

        try {

            $file = $custom_asset->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this custom asset");
            }

            if (!Storage::disk('public')->exists('custom_assets/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($custom_asset->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('custom_assets/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
