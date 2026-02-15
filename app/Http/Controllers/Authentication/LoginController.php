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

        // Try to authenticate as Administrator first
        $user = User::where('email_user', $email)->first();
        if ($user && Hash::check($password, $user->password_user)) {
            auth('administrator')->login($user, false);
            $request->session()->regenerate();
            return redirect()->route('administrators.dashboard');
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
