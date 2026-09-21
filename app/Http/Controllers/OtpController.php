<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\League;
use App\Models\User;
// use App\Rules\ValidPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class OtpController extends Controller
{
    /**
     * Display a registration form with code and league fields.
     * 
     * @param League $league
     * @param string $code
     * @param string $email
     *
     */
    public function register(League $league, string $code, string $email)
    {
        return inertia('auth/OtpRegister', [
            'league' => $league,
            'code' => $code,
            'email'=> $email
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request  $request
     * @param League $league
     * @param string $code
     * @param string $email
     * @return RedirectResponse
     */
    public function store(Request $request, League $league, string $code, string $email): RedirectResponse
    {
        if (User::where('email', $email)->first()) {
            $request->session()->flash('alert', [
                'type' => 'error',
                'message' => 'An account with email: ' . $email . ' already exists. Please, log in.'
            ]);
            return to_route('login');
        }

        if (!Otp::validate($code, $league, $email)) {
            return redirect()->route('otp.register', [$league, $code, $email])->withInput()->withErrors('It appears this code is no longer valid, or is incorrect. <a href="#" class="btn btn-sm btn-primary">Resend Code</a>');
        }

        $registeredAt = Carbon::now();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable'], // [new ValidPhoneNumber],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code' => ['required']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_verified_at' => $registeredAt,
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        $league->members()->attach($user->id);
        
        $user->assignRole('league Member');

        Auth::login($user, false);

        // need to invalidate otp by setting expiration to now
        Otp::where([
            ['email_address', $user->email],
            ['league_id', $league->id]
        ])->update(['expiration' => $registeredAt, 'token_used' => 1]);

        $request->session()->flash('alert', [
            'type' => 'success',
            'message' => 'Welcome to the ' . $league->name . ' poker league. You account has been successfully created'
        ]);

        return to_route('dashboard');  
    }

    /**
     * Display the specified resource.
     */
    public function show(Otp $otp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Otp $otp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Otp $otp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Otp $otp)
    {
        //
    }
}