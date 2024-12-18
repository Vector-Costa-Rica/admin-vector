<?php

namespace App\Http\Controllers;

use App\Models\Project_state;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;

class ProjectStatesController extends Controller
{
    protected Project_state $project_state;

    public function __construct(Project_state $project_state)
    {
        $this->project_state = $project_state;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        try {
            $project_states = Project_state::with('project')->orderBy('created_at', 'desc')->paginate(10);
            return view('admin.project_states.index', compact('project_states'));
        } catch (Exception $e) {
            Log::error('Error fetching project_states: ' . $e->getMessage());
            return view('admin.project_states.index')->with('error', 'Error loading project_states');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.project_states.modals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $project_state = new Project_state();
            $project_state->forceFill($validated);
            $project_state->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Project_state created successfully'
            ]);*/

            return redirect()->route('project_states.index')->with('success', 'Project state added successfully');
        } catch (Exception $e) {
            Log::error('Error creating project state: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error creating project state'
            ], 500);*/

            return back()->with('error', 'Error creating project state');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project_state $project_state): View
    {
        try {
            $project_state->load('project');
            return view('admin.project_states.modals.show', compact('project_state'));
        } catch (Exception $e) {
            Log::error('Error showing project state: ' . $e->getMessage());
            return view('admin.project_states.modals.show')->with('error', 'Error displaying project state details');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project_state $project_state): View
    {
        return view('modals.edit_delete_project_states', compact('project_state'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project_state $project_state): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $project_state->forceFill($validated);
            $project_state->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Project_state updated successfully'
            ]);*/

            return redirect()->route('project_states.index')->with('success', 'Project state updated successfully');
        } catch (Exception $e) {
            Log::error('Error updating project state: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error updating project state'
            ], 500);*/

            return back()->with('error', 'Error updating project state');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project_state $project_state): JsonResponse|RedirectResponse
    {
        try {
            if ($project_state->project()->exists()) {
                /*return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete project state because it has associated projects'
                ], 422);*/

                return back()->with('error', 'Cannot delete project state because it has associated projects');
            }

            $project_state->delete();
            /*return response()->json([
                'success' => true,
                'message' => 'Project_state deleted successfully'
            ]);*/

            return redirect()->route('project_states.index')->with('success', 'Project state deleted successfully');
        } catch (Exception $e) {
            Log::error('Error deleting project state: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error deleting project state'
            ], 500);*/

            return back()->with('error', 'Error deleting project state');
        }
    }
}
