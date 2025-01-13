<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use Exception;
use Illuminate\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\{JsonResponse, Request};


class StatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        try {
            $states = State::with('country')->orderBy('id_country', 'asc')->paginate(30);
            $countries = Country::all();
            return view('admin.states.index', compact('states', 'countries'));
        } catch (Exception $e) {
            Log::error('Error fetching states: ' . $e->getMessage());
            return view('admin.states.index')->with('failed', 'Error fetching states');
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
    public function show(State $states)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $states)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $states)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $states)
    {
        //
    }

    /**
     * @param Country $country
     * @return JsonResponse
     */
    public function getByCountry(Country $country): JsonResponse
    {
        $states = (new State())
            ->where('id_country', $country->id)
            ->select(['id', 'state_name', 'id_country'])
            ->orderBy('state_name')
            ->get();


        /*$states = State::where('id_country', $country->id)
            ->select('id', 'state_name', 'id_country') // Asegúrate de incluir el id
            ->orderBy('state_name')
            ->get();*/

        return response()->json($states);
    }
}
