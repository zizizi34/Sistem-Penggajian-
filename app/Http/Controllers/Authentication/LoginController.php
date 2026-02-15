<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Models\User;
use App\Models\Officer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('authentication.login');
    }

    /**
     * Handle authentication process.
     */
    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Try to authenticate as User (Administrator or Pegawai)
        $user = User::where('email_user', $email)->with('role')->first();

        if ($user && Hash::check($password, $user->password_user)) {
            \Illuminate\Support\Facades\Log::info('User authenticated: ' . $email . ' Role: ' . ($user->role ? $user->role->nama_role : 'No Role'));
            if ($user->role && $user->role->nama_role === 'Pegawai') {
                auth('student')->login($user, false);
                $request->session()->regenerate();
                
                // Debug if session is set
                \Illuminate\Support\Facades\Log::info('Login Session Check: ' . (auth('student')->check() ? 'TRUE' : 'FALSE') . ' UserID: ' . auth('student')->id());

                \Illuminate\Support\Facades\Log::info('Redirecting to students.dashboard');
                return redirect()->route('students.dashboard');
            }

            // Default to Administrator (Director, Manager, HR)
            auth('administrator')->login($user, false);
            $request->session()->regenerate();
             \Illuminate\Support\Facades\Log::info('Redirecting to administrators.dashboard');
            return redirect()->route('administrators.dashboard');
        } else {
             \Illuminate\Support\Facades\Log::info('Authentication failed for: ' . $email);
        }
        
        // Try to authenticate as Officer
        $officer = Officer::where('email', $email)->first();
        if ($officer && Hash::check($password, $officer->password)) {
            auth('officer')->login($officer, false);
            $request->session()->regenerate();
            return redirect()->route('officers.dashboard');
        }

        // If authentication fails
        return redirect()->route('login')
            ->with('authentication', 'Email atau password salah!')
            ->withInput($request->except('password'));
    }
}
