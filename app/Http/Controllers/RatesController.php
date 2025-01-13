<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;

class RatesController extends Controller
{

    protected Rate $rate;

    public function __construct(Rate $rate)
    {
        $this->rate = $rate;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        try {
            $rates = Rate::with('service')->orderBy('created_at', 'desc')->paginate(10);
            return view('admin.rates.index', compact('rates'));
        } catch (Exception $e) {
            Log::error('Error fetching rates: ' . $e->getMessage());
            return view('admin.rates.index')->with('error', 'Error loading rates');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.rates.modals.create');
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

            $rate = new Rate();
            $rate->forceFill($validated);
            $rate->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Rate created successfully'
            ]);*/

            return redirect()->route('rates.index')->with('success', 'Rate added successfully');
        } catch (Exception $e) {
            Log::error('Error creating rate: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error creating rate'
            ], 500);*/

            return back()->with('error', 'Error creating rate');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate): View
    {
        try {
            $rate->load('service');
            return view('admin.rates.modals.show', compact('rate'));
        } catch (Exception $e) {
            Log::error('Error showing rate: ' . $e->getMessage());
            return view('admin.rates.modals.show')->with('error', 'Error displaying rate details');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rate $rate): View
    {
        return view('modals.edit_delete_rates', compact('rate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rate $rate): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $rate->forceFill($validated);
            $rate->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Rate updated successfully'
            ]);*/

            return redirect()->route('rates.index')->with('success', 'Rate updated successfully');
        } catch (Exception $e) {
            Log::error('Error updating rate: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error updating rate'
            ], 500);*/

            return back()->with('error', 'Error updating rate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate): JsonResponse|RedirectResponse
    {
        try {
            if ($rate->service()->exists()) {
                /*return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete rate because it has associated services'
                ], 422);*/

                return back()->with('error', 'Cannot delete rate because it has associated services');
            }

            $rate->delete();
            /*return response()->json([
                'success' => true,
                'message' => 'Rate deleted successfully'
            ]);*/

            return redirect()->route('rates.index')->with('success', 'Rate deleted successfully');
        } catch (Exception $e) {
            Log::error('Error deleting rate: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error deleting rate'
            ], 500);*/

            return back()->with('error', 'Error deleting rate');
        }
    }
}
