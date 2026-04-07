<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administrator\StoreOfficerRequest;
use App\Http\Requests\Administrator\UpdateOfficerRequest;
use App\Models\Officer;
use App\Models\Departemen;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $officers = Officer::with('departemen')->get();
        $departemens = Departemen::all();

        return view('administrator.officer.index', compact('officers', 'departemens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfficerRequest $request)
    {
        $validated = $request->validated();

        Officer::create([
            'id_departemen' => $request->id_departemen,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone_number' => $validated['phone_number'],
        ]);

        return redirect()->route('administrators.officers.index')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficerRequest $request, Officer $officer)
    {
        $validated = $request->validated();
        
        $data = [
            'id_departemen' => $validated['id_departemen'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($validated['password']);
        }

        $officer->update($data);

        return redirect()->route('administrators.officers.index')->with('success', 'Data berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Officer $officer)
    {
        $officer->delete();

        return redirect()->route('administrators.officers.index')->with('success', 'Data berhasil dihapus!');
    }
}
