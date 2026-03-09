<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\PtkpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index()
    {
        $officer = auth('officer')->user();
        $query = Pegawai::with('departemen', 'jabatan');
        
        if ($officer->id_departemen) {
            $query->where('id_departemen', $officer->id_departemen);
        }
        
        $pegawai = $query->get();
        return view('officer.pegawai.index', compact('pegawai'));
    }

    public function show($id)
    {
        $officer = auth('officer')->user();
        $pegawai = Pegawai::with('departemen', 'jabatan')->findOrFail($id);

        if ($officer->id_departemen && $pegawai->id_departemen != $officer->id_departemen) {
            return redirect()->route('officers.pegawai.index')->with('error', 'Anda tidak berhak melihat pegawai di departemen lain.');
        }

        $today = now()->format('Y-m-d');
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $attendanceToday = \App\Models\Absensi::where('id_pegawai', $pegawai->id_pegawai)
            ->whereDate('tanggal_absensi', $today)
            ->first();

        $totalHadir = \App\Models\Absensi::where('id_pegawai', $pegawai->id_pegawai)
            ->whereMonth('tanggal_absensi', $currentMonth)
            ->whereYear('tanggal_absensi', $currentYear)
            ->whereIn('status', ['hadir', 'terlambat'])->count();

        $totalIzin = \App\Models\Absensi::where('id_pegawai', $pegawai->id_pegawai)
            ->whereMonth('tanggal_absensi', $currentMonth)
            ->whereYear('tanggal_absensi', $currentYear)
            ->where('status', 'izin')->count();

        $totalAlpha = \App\Models\Absensi::where('id_pegawai', $pegawai->id_pegawai)
            ->whereMonth('tanggal_absensi', $currentMonth)
            ->whereYear('tanggal_absensi', $currentYear)
            ->where('status', 'alpha')->count();

        return view('officer.pegawai.show', compact('pegawai', 'attendanceToday', 'totalHadir', 'totalIzin', 'totalAlpha', 'today'));
    }

    public function create()
    {
        $officer = auth('officer')->user();
        
        if (!$officer->id_departemen) {
            return redirect()->route('officers.pegawai.index')->with('error', 'Anda belum memiliki departemen.');
        }

        // Filter jabatan hanya yang sesuai departemen officer yang login
        $jabatan = Jabatan::where('id_departemen', $officer->id_departemen)
            ->orderBy('nama_jabatan')
            ->get();
        $ptkpStatus = PtkpStatus::all();

        return view('officer.pegawai.create', compact('jabatan', 'ptkpStatus'));
    }

    public function store(Request $request)
    {
        $officer = auth('officer')->user();
        
        if (!$officer->id_departemen) {
            return redirect()->route('officers.pegawai.index')->with('error', 'Anda belum memiliki departemen.');
        }

        $validated = $request->validate([
            'nik_pegawai' => 'required|string|unique:pegawai,nik_pegawai|max:50',
            'nama_pegawai' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email_pegawai' => 'required|email|unique:pegawai,email_pegawai|max:100',
            'bank_pegawai' => 'nullable|string|max:50',
            'no_rekening' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:50',
            'id_ptkp_status' => 'nullable|exists:ptkp_status,id_ptkp_status',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'status_pegawai' => 'required|in:aktif,non-aktif',
            'tgl_masuk' => 'required|date',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'password' => 'required|string|min:6',
        ]);

        $validated['id_departemen'] = $officer->id_departemen;

        if (empty($validated['gaji_pokok'])) {
            $jabatan = Jabatan::find($validated['id_jabatan']);
            $validated['gaji_pokok'] = $jabatan ? $jabatan->min_gaji : 0;
        }

        $pegawai = Pegawai::create($validated);

        // Buat akun User otomatis untuk pegawai ini agar bisa login
        $role = DB::table('role')->where('nama_role', 'Pegawai')->first();
        if ($role) {
            User::create([
                'email_user' => $pegawai->email_pegawai,
                'password_user' => Hash::make($validated['password']),
                'id_role' => $role->id_role,
                'id_pegawai' => $pegawai->id_pegawai,
            ]);
        }

        return redirect()->route('officers.pegawai.index')->with('success', 'Pegawai berhasil ditambahkan ke departemen Anda beserta akun login.');
    }

    public function edit($id)
    {
        $officer = auth('officer')->user();
        $pegawai = Pegawai::with('departemen', 'jabatan')->findOrFail($id);

        // Pastikan pegawai milik departemen officer ini
        if ($officer->id_departemen && $pegawai->id_departemen != $officer->id_departemen) {
            return redirect()->route('officers.pegawai.index')->with('error', 'Anda tidak berhak mengedit pegawai di departemen lain.');
        }

        // Filter jabatan hanya yang sesuai departemen officer yang login
        $jabatan = Jabatan::where('id_departemen', $officer->id_departemen)
            ->orderBy('nama_jabatan')
            ->get();
        $ptkpStatus = PtkpStatus::all();

        return view('officer.pegawai.edit', compact('pegawai', 'jabatan', 'ptkpStatus'));
    }

    public function update(Request $request, $id)
    {
        $officer = auth('officer')->user();
        $pegawai = Pegawai::findOrFail($id);

        // Pastikan pegawai milik departemen officer ini
        if ($officer->id_departemen && $pegawai->id_departemen != $officer->id_departemen) {
            return redirect()->route('officers.pegawai.index')->with('error', 'Anda tidak berhak mengedit pegawai di departemen lain.');
        }

        $validated = $request->validate([
            'nik_pegawai'    => 'required|string|max:50|unique:pegawai,nik_pegawai,' . $pegawai->id_pegawai . ',id_pegawai',
            'nama_pegawai'   => 'required|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'required|date',
            'alamat'         => 'required|string',
            'no_hp'          => 'required|string|max:20',
            'email_pegawai'  => 'required|email|max:100|unique:pegawai,email_pegawai,' . $pegawai->id_pegawai . ',id_pegawai',
            'bank_pegawai'   => 'nullable|string|max:50',
            'no_rekening'    => 'nullable|string|max:50',
            'npwp'           => 'nullable|string|max:50',
            'id_ptkp_status' => 'nullable|exists:ptkp_status,id_ptkp_status',
            'id_jabatan'     => 'required|exists:jabatan,id_jabatan',
            'status_pegawai' => 'required|in:aktif,non-aktif',
            'tgl_masuk'      => 'required|date',
            'gaji_pokok'     => 'nullable|numeric|min:0',
            'password'       => 'nullable|string|min:6',
        ]);

        // Jika gaji_pokok kosong, ambil dari jabatan
        if (empty($validated['gaji_pokok'])) {
            $jabatanObj = Jabatan::find($validated['id_jabatan']);
            $validated['gaji_pokok'] = $jabatanObj ? $jabatanObj->min_gaji : 0;
        }

        // Jangan biarkan departemen berubah
        $validated['id_departemen'] = $officer->id_departemen;

        // Hapus password dari data pegawai agar tidak masuk ke tabel pegawai
        $newPassword = $validated['password'] ?? null;
        unset($validated['password']);

        $pegawai->update($validated);

        // Update password akun User jika diisi
        if ($newPassword) {
            $user = User::where('id_pegawai', $pegawai->id_pegawai)->first();
            if ($user) {
                $user->update(['password_user' => Hash::make($newPassword)]);
            }
        }

        return redirect()->route('officers.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $officer = auth('officer')->user();
        $pegawai = Pegawai::findOrFail($id);

        // Pastikan pegawai milik departemen officer ini
        if ($officer->id_departemen && $pegawai->id_departemen != $officer->id_departemen) {
            return redirect()->route('officers.pegawai.index')->with('error', 'Anda tidak berhak menghapus pegawai di departemen lain.');
        }

        // Hapus akun user yang terhubung
        User::where('id_pegawai', $pegawai->id_pegawai)->delete();

        $pegawai->delete();

        return redirect()->route('officers.pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
