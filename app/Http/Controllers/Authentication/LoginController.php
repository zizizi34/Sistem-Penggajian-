<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Models\User;
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

        // Find user by email_user field
        $user = User::where('email_user', $email)->first();

        // Verify password
        if ($user && Hash::check($password, $user->password_user)) {
            // Authenticate the user on the 'administrator' guard so middleware matches
            auth('administrator')->login($user, false);
            $request->session()->regenerate();

            return redirect()->route('administrators.dashboard');
        }

        return redirect()->route('login')->with('authentication', 'Email atau password salah!')->withInput();
    }
}
