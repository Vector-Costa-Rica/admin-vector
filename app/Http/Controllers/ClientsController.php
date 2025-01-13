<?php

namespace App\Http\Controllers;

use App\Models\{
    Client,
    Language,
    City,
    State,
    Country
};
use Exception;
use Illuminate\{Http\Request,
    Http\Response,
    Http\RedirectResponse,
    Contracts\View\View,
    Contracts\View\Factory,
    Foundation\Application,
    Support\Facades\Log,
    Support\Facades\Storage
};

class ClientsController extends Controller
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $languages = Language::all();
        $cities = City::all();
        $states = State::all();
        $countries = Country::all();
        $clients = Client::query()->latest()->paginate(30);

        return view('admin.clients.index',
            compact('clients', 'languages', 'cities', 'states', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'picture' => 'required|file',
                'id_city' => 'required|exists:cities,id',
                'address' => 'required|string',
                'zip' => 'required|integer',
                'phone' => 'required|string',
                'mobile' => 'required|string',
                'email' => 'required|email',
                'web' => 'required|string',
                'language_id' => 'required|exists:languages,id'
            ]);

            if ($request->hasFile('picture')) {
                $picture = $request->file('picture');
                $picturename = time() . '_' . $picture->getClientOriginalName();
                $picture->storeAs('clients', $picturename, 'public');

                $client = new Client();
                $client->forceFill([
                    'name' => $validated['name'],
                    'picture' => $picturename,
                    'id_city' => $validated['id_city'],
                    'address' => $validated['address'],
                    'zip' => $validated['zip'],
                    'phone' => $validated['phone'],
                    'mobile' => $validated['mobile'],
                    'email' => $validated['email'],
                    'web' => $validated['web'],
                    'language_id' => $validated['language_id']
                ]);
                $client->save();

                return redirect()->route('clients.index')->with('success', 'Client added successfully');
            }

            return back()->with('error', 'No picture uploaded');

        } catch (Exception $e) {
            Log::error('Error creating client: ' . $e->getMessage());

            return back()->with('error', 'Error creating client');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Factory|View|Application
    {
        $client = $this->client->getClientById($id);
        return view('clients.show', ['clients' => $client]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): Response|Factory|Application|View
    {
        return view('modals.edit_delete_clients')->with(['client' => $client]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): Request|Response|RedirectResponse
    {
        try {
            Log::info('Datos recibidos en update:', $request->all());

            $validated = $request->validate([
                'name' => 'sometimes|required|string',
                'picture' => 'nullable|file',
                'id_city' => 'sometimes|exists:cities,id',
                'address' => 'sometimes|required|string',
                'zip' => 'sometimes|required|integer',
                'phone' => 'sometimes|required|string',
                'mobile' => 'sometimes|required|string',
                'email' => 'sometimes|required|email',
                'web' => 'sometimes|required|string',
                'language_id' => 'sometimes|exists:languages,id'
            ]);

            Log::info('Datos validados:', $validated);

            // Crear array de datos a actualizar
            $updateData = [];

            // Actualizar solo los campos que han sido enviados
            if ($request->filled('name')) $updateData['name'] = $request->input('name');
            if ($request->filled('address')) $updateData['address'] = $request->input('address');
            if ($request->filled('zip')) $updateData['zip'] = $request->input('zip');
            if ($request->filled('phone')) $updateData['phone'] = $request->input('phone');
            if ($request->filled('mobile')) $updateData['mobile'] = $request->input('mobile');
            if ($request->filled('email')) $updateData['email'] = $request->input('email');
            if ($request->filled('web')) $updateData['web'] = $request->input('web');
            if ($request->filled('language_id')) $updateData['language_id'] = $request->input('language_id');
            if ($request->filled('id_city')) $updateData['id_city'] = $request->input('id_city');

            Log::info('Datos a actualizar:', $updateData);

            // Manejar la imagen si se proporciona una nueva
            if ($request->hasFile('picture')) {
                $currentFile = $client->getAttribute('picture');
                if ($currentFile) {
                    Storage::disk('public')->delete('clients/' . $currentFile);
                }

                $picture = $request->file('picture');
                $picturename = time() . '_' . $picture->getClientOriginalName();
                $picture->storeAs('clients', $picturename, 'public');

                $updateData['picture'] = $picturename;
            }

            Log::info('Cliente antes de actualizar:', $client->toArray());

            // Intentar la actualización
            $updated = $client->forceFill($updateData)->save();

            Log::info('¿Actualización exitosa?:', ['success' => $updated]);
            Log::info('Cliente después de actualizar:', $client->fresh()->toArray());

            if ($updated) {
                return redirect()->route('clients.index')->with('success', 'Client updated successfully');
            } else {
                return back()->with('error', 'Failed to update client');
            }

        } catch (Exception $e) {
            Log::error('Error updating client: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error updating client: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): Response|RedirectResponse
    {
        $picture = $client->getAttribute('picture');
        Storage::disk('public')->delete('clients/' . $picture);

        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
    }
}
