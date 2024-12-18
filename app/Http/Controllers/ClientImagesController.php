<?php

namespace App\Http\Controllers;

use App\Models\Client_image;
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

class ClientImagesController extends Controller
{

    protected Client_image $client_image;

    public function __construct(Client_image $client_image)
    {
        $this->client_image = $client_image;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $projects = Project::all();
        $client_images = Client_image::query()->latest()->paginate(10);
        return view('admin.client_images.index')
            ->with(['client_images' => $client_images, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('client_images.create');
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
            $file->storeAs('client_images', $filename, 'public');

            $client_image = new Client_image();
            $client_image->forceFill([
                'project_id' => $validated['project_id'],
                'file' => $filename
            ]);
            $client_image->save();

            return redirect()->route('client_images.index')->with('success', 'Client image added successfully');
        }

        return back()->with('error', 'No file uploaded');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $client_image = $this->client_image->getClient_imageById($id);
        return view('client_images.show', ['client_images' => $client_image]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client_image $client_image): Response|Factory|Application|View
    {
        return view('modals.edit_delete_client_images')->with(['client_image' => $client_image]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client_image $client_image): Request|Response|RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'file' => 'nullable|file|max:2048'
        ]);

        $updateData = [
            'project_id' => $validated['project_id']
        ];

        if ($request->hasFile('file')) {
            $currentFile = $client_image->getAttribute('file');
            Storage::disk('public')->delete('client_images/' . $currentFile);

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('client_images', $filename, 'public');

            $updateData['file'] = $filename;
        }

        $client_image->forceFill($updateData);
        $client_image->save();

        return redirect()->route('client_images.index')->with('success', 'Client image updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client_image $client_image): Response|RedirectResponse
    {
        $file = $client_image->getAttribute('file');
        Storage::disk('public')->delete('client_images/' . $file);

        $client_image->delete();
        return redirect()->route('client_images.index')->with('success', 'Client image deleted successfully');
    }

    public function download(Client_image $client_image): StreamedResponse|RedirectResponse
    {

        try {

            $file = $client_image->getAttribute('file');

            if (empty($file)) {
                throw new Exception("Not file associated with this client image");
            }

            if (!Storage::disk('public')->exists('client_images/' . $file)) {
                throw new Exception('File not found');
            }

            $originalName = substr($client_image->getAttribute('file'), strpos($file, '_') + 1);

            if (empty($originalName)) {
                throw new Exception('Original file name could not be determined');
            }

            return Storage::disk('public')->download('client_images/' . $file, $originalName);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }
}
