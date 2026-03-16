<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateProfileSettingRequest;
use App\Models\Student;

class ProfileSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth('student')->user();
        $pegawai = $user->pegawai;
        
        $myInformation = (object) [
            'name' => $pegawai->nama_pegawai ?? '',
            'email' => $pegawai->email_pegawai ?? $user->email_user,
            'phone_number' => $pegawai->no_hp ?? '',
        ];

        return view('student.profile_setting.index', compact('myInformation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileSettingRequest $request)
    {
        $user = auth('student')->user();
        $pegawai = $user->pegawai;
        $validated = $request->validated();

        if ($request->filled('password')) {
            $user->update([
                'password_user' => bcrypt($validated['password']),
            ]);
        }
        
        if ($pegawai) {
            $pegawai->update([
                'nama_pegawai' => $request->filled('name') ? $validated['name'] : $pegawai->nama_pegawai,
                'email_pegawai' => $request->filled('email') ? $validated['email'] : $pegawai->email_pegawai,
                'no_hp' => $request->filled('phone_number') ? $validated['phone_number'] : $pegawai->no_hp,
            ]);
        }

        if ($request->filled('email')) {
             $user->update(['email_user' => $validated['email']]);
        }

        return redirect()->route('students.profile-settings.index')->with('success', 'Data berhasil diubah!');
    }
}
