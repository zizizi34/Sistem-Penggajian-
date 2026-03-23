<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\PtkpStatus;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::with('departemen', 'jabatan')->get();
        $departemens = Departemen::all();
        $jabatans = Jabatan::all();
        $ptkpStatus = PtkpStatus::all();
        
        return view('administrator.pegawai.index', compact('pegawai', 'departemens', 'jabatans', 'ptkpStatus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik_pegawai' => 'required|string|unique:pegawai,nik_pegawai|max:50',
            'nama_pegawai' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email_pegawai' => 'required|email|unique:pegawai,email_pegawai|max:100',
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_ptkp_status' => 'nullable|exists:ptkp_status,id_ptkp_status',
            'status_pegawai' => 'required|in:aktif,nonaktif',
            'tgl_masuk' => 'required|date',
            'gaji_pokok' => 'required|numeric|min:0',
            'password' => 'required|string|min:6',
        ]);

        $pegawai = Pegawai::create($validated);

        // Buat akun User otomatis
        $role = DB::table('role')->where('nama_role', '=', 'Pegawai', 'and')->first();
        if ($role) {
            User::create([
                'email_user' => $pegawai->email_pegawai,
                'password_user' => Hash::make($validated['password']),
                'id_role' => $role->id_role,
                'id_pegawai' => $pegawai->id_pegawai,
            ]);
        }

        return redirect()->route('administrators.pegawai.index')->with('success', 'Pegawai berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $validated = $request->validate([
            'nik_pegawai' => 'required|string|max:50|unique:pegawai,nik_pegawai,' . $pegawai->id_pegawai . ',id_pegawai',
            'nama_pegawai' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email_pegawai' => 'required|email|max:100|unique:pegawai,email_pegawai,' . $pegawai->id_pegawai . ',id_pegawai',
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_ptkp_status' => 'nullable|exists:ptkp_status,id_ptkp_status',
            'status_pegawai' => 'required|in:aktif,nonaktif',
            'tgl_masuk' => 'required|date',
            'gaji_pokok' => 'required|numeric|min:0',
            'password' => 'nullable|string|min:6',
        ]);

        $password = $validated['password'] ?? null;
        unset($validated['password']);

        $pegawai->update($validated);

        if ($password) {
            $user = User::where('id_pegawai', '=', $pegawai->id_pegawai, 'and')->first();
            if ($user) {
                $user->update(['password_user' => Hash::make($password)]);
            }
        }

        return redirect()->route('administrators.pegawai.index')->with('success', 'Pegawai berhasil diperbarui');
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load('departemen', 'jabatan', 'ptkpStatus');
        return view('administrator.pegawai.show', compact('pegawai'));
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        User::where('id_pegawai', '=', $pegawai->id_pegawai, 'and')->delete();
        $pegawai->delete();
        
        return redirect()->route('administrators.pegawai.index')->with('success', 'Pegawai berhasil dihapus');
    }
}
