<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use Exception;
use Illuminate\{
    Http\Request,
    Http\Response,
    Http\RedirectResponse,
    Contracts\View\View,
    Contracts\View\Factory,
    Foundation\Application,
    Support\Facades\Storage
};
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetsController extends Controller
{

    protected Asset $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $assets = Asset::query()->latest()->paginate(10);
        return view('admin.assets.index')->with(['assets' => $assets, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('assets.create');
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
            // Crear el path para el proyecto
            $projectPath = 'assets/project_' . $validated['project_id'];

            // Asegurarnos de que el directorio existe
            if (!Storage::disk('public')->exists($projectPath)) {
                Storage::disk('public')->makeDirectory($projectPath);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs($projectPath, $filename, 'public');

            $asset = new Asset();
            $asset->fill([
                'project_id' => $validated['project_id'],
                'file' => $projectPath . '/' . $filename
            ]);
            $asset->save();

            return redirect()->route('assets.index')->with('success', 'Asset added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $asset = $this->asset->getAssetById($id);
        return view('assets.show', ['assets' => $asset]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset): Response|Factory|Application|View
    {
        return view('modals.edit_delete_assets')->with(['asset' => $asset]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            // Obtener y eliminar el archivo actual
            $currentFile = $asset->getAttribute('file');
            Storage::disk('public')->delete($currentFile);

            // Crear el path para el proyecto
            $projectPath = 'assets/project_' . $validated['project_id'];

            // Asegurarnos de que el directorio existe
            if (!Storage::disk('public')->exists($projectPath)) {
                Storage::disk('public')->makeDirectory($projectPath);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs($projectPath, $filename, 'public');

            $updateData['file'] = $projectPath . '/' . $filename;
        }

        $asset->forceFill($updateData);
        $asset->save();

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset): Response|RedirectResponse
    {
        // Eliminar el archivo físico
        Storage::disk('public')->delete($asset->getAttribute('file'));

        // Verificar si el directorio está vacío y eliminarlo si es así
        $projectPath = dirname($asset->getAttribute('file'));
        if (Storage::disk('public')->exists($projectPath)) {
            $filesInDirectory = Storage::disk('public')->files($projectPath);
            if (empty($filesInDirectory)) {
                Storage::disk('public')->deleteDirectory($projectPath);
            }
        }

        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully');
    }

    public function download(Asset $asset): StreamedResponse|RedirectResponse
    {

        try {
            $file = $asset->getAttribute('file');

            if (empty($file)) {
                throw new Exception("No file associated with this asset");
            }

            if (!Storage::disk('public')->exists($file)) {
                throw new Exception('File not found');
            }

            // Obtener solo el nombre del archivo original sin el timestamp
            $originalName = substr(basename($file), strpos(basename($file), '_') + 1);

            return Storage::disk('public')->download($file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
