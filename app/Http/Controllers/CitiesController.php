<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\{
    Request,
    JsonResponse
};
use App\Models\Country;
use App\Models\State;
use Exception;
use Illuminate\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        try {
            $cities = City::with(['state', 'state.country'])
                ->orderBy('id_state', 'asc')
                ->paginate(100);

            $countries = Country::all();
            $states = State::all();

            return view('admin.cities.index', compact('cities', 'countries', 'states'));
        } catch (Exception $e) {
            Log::error('Error fetching cities list: ' . $e->getMessage());
            return view('admin.cities.index')->with('failed', 'Error fetching cities');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(City $cities)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $cities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $cities)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $cities)
    {
        //
    }

    public function getByState(State $state): JsonResponse
    {
        $cities = City::where('id_state', $state->id)
            ->orderBy('city')
            ->get();

        return response()->json($cities);
    }

    public function getById($id): JsonResponse
    {
        $city = City::with(['state.country'])->find($id);
        return response()->json([
            'city' => [
                'id' => $city->id,
                'name' => $city->city,
            ],
            'state' => [
                'id' => $city->state->id,
                'name' => $city->state->state_name
            ],
            'country' => [
             'id' => $city->state->country->id,
             'name' => $city->state->country->country_name
            ]
        ]);
    }
}
