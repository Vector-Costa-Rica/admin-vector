<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;

class CountriesController extends Controller
{

    protected Country $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        try {
            $countries = Country::all();
            return view('admin.countries.index', compact('countries'));
        } catch (Exception $e) {
            Log::error('Error fetching countries: ' . $e->getMessage());
            return view('admin.countries.index')->with('error', 'There was an error fetching countries');
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
    public function show(Country $countries)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $countries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $countries)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $countries)
    {
        //
    }
}
