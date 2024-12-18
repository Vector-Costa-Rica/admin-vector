<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Rate;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        try {
            $services = Service::with('rate')->orderBy('created_at', 'desc')->paginate(10);
            $rates = Rate::all();
            return view('admin.services.index', compact('services', 'rates'));
        } catch (Exception $e) {
            Log::error('Error fetching services: ' . $e->getMessage());
            return view('admin.services.index')->with('error', 'Error loading services');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        try {
            $rates = Rate::all();
            return view('admin.services.modals.create', compact('rates'));
        } catch (Exception $e) {
            Log::error('Error loading create service form: ' . $e->getMessage());
            return view('admin.services.modals.create')->with('error', 'Error loading create form');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'product' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'rate_id' => 'required|exists:rates,id'
            ]);

            $service = new Service();
            $service->forceFill($validated);
            $service->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Service created successfully'
            ]);*/

            return redirect()->route('services.index')->with('success', 'Service added successfully');
        } catch (Exception $e) {
            Log::error('Error creating service: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error creating service'
            ], 500);*/

            return back()->with('error', 'Error creating service');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): View
    {
        try {
            $service->load('rate');
            return view('admin.services.modals.show', compact('service'));
        } catch (Exception $e) {
            Log::error('Error showing service: ' . $e->getMessage());
            return view('admin.services.modals.show')->with('error', 'Error displaying service details');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): View
    {
        try {
            $rates = Rate::all();
            return view('admin.services.modals.edit', compact('service', 'rates'));
        } catch (Exception $e) {
            Log::error('Error loading edit service form: ' . $e->getMessage());
            return view('admin.services.modals.edit')->with('error', 'Error loading edit form');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'product' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'rate_id' => 'required|exists:rates,id'
            ]);

            $service->forceFill($validated);
            $service->save();

            /*return response()->json([
                'success' => true,
                'message' => 'Service updated successfully'
            ]);*/

            return redirect()->route('services.index')->with('success', 'Service updated successfully');
        } catch (Exception $e) {
            Log::error('Error updating service: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error updating service'
            ], 500);*/

            return back()->with('error', 'Error updating service');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): JsonResponse|RedirectResponse
    {
        try {
            $service->delete();
            /*return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);*/

            return redirect()->route('rates.index')->with('success', 'Rate deleted successfully');
        } catch (Exception $e) {
            Log::error('Error deleting service: ' . $e->getMessage());
            /*return response()->json([
                'success' => false,
                'message' => 'Error deleting service'
            ], 500);*/

            return back()->with('error', 'Error deleting rate');
        }
    }

    /**
     * Get service by ID.
     *
     * @param int $id
     * @return Service|null
     */
    /*public function getServiceById(int $id): ?Service
    {
        try {
            return Service::with('rate')->find($id);
        } catch (Exception $e) {
            Log::error('Error getting service by ID: ' . $e->getMessage());
            return null;
        }
    }*/

    /**
     * Get services by rate.
     */
    /*public function getByRate(Rate $rate): View
    {
        try {
            $services = $rate->service()->paginate(10);
            return view('admin.services.by-rate', compact('services', 'rate'));
        } catch (Exception $e) {
            Log::error('Error fetching services by rate: ' . $e->getMessage());
            return view('admin.services.by-rate')->with('error', 'Error loading services for this rate');
        }
    }*/
}
