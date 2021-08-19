<?php

namespace App\Http\Controllers\AuthSso;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredLoginController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    { 
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|string|email|max:255',
            'id_sso' => 'required',
            // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('id_sso', $request->id_sso)->first();
        if ($user == null) {
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make('xxxxxxxx'),
                'id_sso'    => $request->id_sso,
            ]);
            event(new Registered($user));
        }

        if ($user) {
            Auth::login($user);
        }

        //return redirect(RouteServiceProvider::HOME);
        return response()->json([
            'status' => true,
        ]);
    }
}
