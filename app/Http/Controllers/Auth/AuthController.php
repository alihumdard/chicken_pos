<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service; // <-- Import Service model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // <-- Import DB for transactions
use App\Models\Event;

class AuthController extends Controller
{

    public function index()
    {
        $user = auth()->user() ?? '';

        if ($user ?? '') {
            if ($user->role == 'Admin') {
                //
            } else {
                //
            }

            return view('pages.dashboard');
        } else {
            return view('pages.auth.login');
        }
    }

    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->withTrashed()->first();

        if ($user && $user->trashed()) {
            return back()->withErrors(['email' => 'Your account has been deactivated. Please contact an administrator to reactivate it.'])->onlyInput('email');
        }

        // THE FIX IS HERE: We pass the 'remember' checkbox value to Auth::attempt()
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $loggedInUser = Auth::user();

            if ($loggedInUser->status == 3) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is currently suspended. Please contact an administrator.'])->onlyInput('email');
            }

            if ($loggedInUser) {
                return redirect()->route('dashboard');
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    public function showRegisterForm()
    {
        return view('pages.auth.signup');
    }

    public function register(Request $request)
    {
        // ## 1. Add validation for all new fields
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'club' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'array'],
            'services.*' => ['string'], // Ensures selected services are valid
            'terms' => ['accepted'], // Ensures terms checkbox is checked
            'privacy' => ['accepted'], // Ensures privacy checkbox is checked
        ]);

        // ## 2. Use a database transaction for safety
        DB::beginTransaction();
        try {
            // ## 3. Create the user with the new fields
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'com_name' => $request->club,
                'address' => $request->location,
                'subscribed_to_newsletter' => $request->boolean('newsletter'),
                'terms_accepted_at' => now(),
                'privacy_policy_accepted_at' => now(),
                'role' => 'User', // Hardcoded as 'User'
                'status' => '2',
            ]);


            DB::commit(); // If everything is successful, commit the transaction

        } catch (\Exception $e) {
            DB::rollBack(); // If anything fails, undo all database changes
            // You can also log the error: \Log::error($e);
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
};
