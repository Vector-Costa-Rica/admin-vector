<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;

class LanguagesController extends Controller
{
    protected Language $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        try {
            $languages = Language::with('client')->orderBy('created_at', 'desc')->paginate(10);
            return view('admin.languages.index', compact('languages'));
        } catch (Exception $e) {
            Log::error('Error fetching languages: ' . $e->getMessage());
            return view('admin.languages.index')->with('error', 'Error loading languages');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.languages.modals.create');
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

            $language = new Language();
            $language->forceFill($validated);
            $language->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Language created successfully'
            ]);*/

            return redirect()->route('languages.index')->with('success', 'Language added successfully');
        } catch (Exception $e) {
            Log::error('Error creating language: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error creating language'
            ], 500);*/

            return back()->with('error', 'Error creating language');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language): View
    {
        try {
            $language->load('client');
            return view('admin.languages.modals.show', compact('language'));
        } catch (Exception $e) {
            Log::error('Error showing language: ' . $e->getMessage());
            return view('admin.languages.modals.show')->with('error', 'Error displaying language details');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language): View
    {
        return view('modals.edit_delete_languages', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $language->forceFill($validated);
            $language->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Language updated successfully'
            ]);*/

            return redirect()->route('languages.index')->with('success', 'Language updated successfully');
        } catch (Exception $e) {
            Log::error('Error updating language: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error updating language'
            ], 500);*/

            return back()->with('error', 'Error updating language');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language): JsonResponse|RedirectResponse
    {
        try {
            if ($language->client()->exists()) {
                /*return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete language because it has associated clients'
                ], 422);*/

                return back()->with('error', 'Cannot delete language because it has associated clients');
            }

            $language->delete();
            /*return response()->json([
                'success' => true,
                'message' => 'Language deleted successfully'
            ]);*/

            return redirect()->route('languages.index')->with('success', 'Language deleted successfully');
        } catch (Exception $e) {
            Log::error('Error deleting language: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error deleting language'
            ], 500);*/

            return back()->with('error', 'Error deleting language');
        }
    }
}
