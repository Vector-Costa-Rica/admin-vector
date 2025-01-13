<?php

namespace App\Http\Controllers;

use App\Models\{Project,
    Client,
    Project_state,
    Client_image,
    Client_doc,
    Asset,
    Operational_doc,
    Branding,
    Tech_doc,
    Custom_asset,
    Report,
    Proposal
};
use Exception;
use Illuminate\Http\{JsonResponse, Request, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\{
    Log,
    DB,
    Storage,
    Validator
};
use Illuminate\Validation\ValidationException;

class ProjectsController extends Controller
{
    protected Project $project;

    public function __construct(Project $project){
        $this->project = $project;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): view
    {
        try {
            $clients = Client::all();
            $client_images = Client_image::all();
            $client_docs = Client_doc::all();
            $assets = Asset::all();
            $operational_docs = Operational_doc::all();
            $brandings = Branding::all();
            $tech_docs = Tech_doc::all();
            $custom_assets = Custom_asset::all();
            $reports = Report::all();
            $proposals = Proposal::all();
            $project_states = Project_state::all();
            $projects = Project::with('clients')->latest()->paginate(30);
            return view('admin.projects.index',
                compact('clients',
                    'client_images',
                    'client_docs',
                    'assets',
                    'operational_docs',
                    'brandings',
                    'tech_docs',
                    'custom_assets',
                    'reports',
                    'proposals',
                    'project_states',
                    'projects'));
        }catch (Exception $e) {
return view('admin.projects.index')->with('error', 'Error loading projects');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try{

            $clients = Client::all();
            $project_states = Project_state::all();
return view('admin.projects.actions.create', compact('clients', 'project_states'));
        } catch (Exception $e) {
            return view('admin.projects.actions.create')->with('error', 'Error loading create form');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Usar Validator facade
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'client_id' => 'required|exists:clients,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'budget' => 'required|numeric',
                'repo' => 'nullable|url',
                'url' => 'nullable|url',
                'server' => 'nullable|string',
                'state' => 'required|exists:project_states,id',
                'status' => 'required|in:active,inactive,on_hold,completed',
                'condition' => 'nullable|string',
                'assets.*' => 'nullable|file'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();

            // Crear el proyecto usando fill
            $project = new Project();
            $project->fill([
                'name' => $validated['name'],
                'client_id' => $validated['client_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'budget' => $validated['budget'],
                'repo' => $validated['repo'],
                'url' => $validated['url'],
                'server' => $validated['server'],
                'state' => $validated['state'],
                'status' => $validated['status'],
                'condition' => $validated['condition']
            ]);
            $project->save();

            // Procesar los assets si se han subido
            if ($request->hasFile('assets')) {
                // Crear el directorio para el proyecto si no existe
                $projectPath = 'assets/project_' . $project->getAttribute('id');

                // Asegurarnos de que el directorio existe
                if (!Storage::disk('public')->exists($projectPath)) {
                    Storage::disk('public')->makeDirectory($projectPath);
                }

                foreach ($request->file('assets') as $file) {
                    // Generar un nombre único para el archivo
                    $filename = time() . '_' . $file->getClientOriginalName();

                    // Guardar el archivo en el directorio del proyecto
                    $file->storeAs($projectPath, $filename, 'public');

                    // Crear el asset usando fill
                    $asset = new Asset();
                    $asset->fill([
                        'project_id' => $project->getAttribute('id'),
                        'file' => $projectPath . '/' . $filename // Guardar la ruta relativa completa
                    ]);
                    $asset->save();
                }
            }

            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Project created successfully with assets');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating project: ' . $e->getMessage());
            return back()->with('error', 'Error creating project: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $projects)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $projects)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $projects)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $projects)
    {
        //
    }
}
