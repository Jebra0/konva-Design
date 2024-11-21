<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect($provider){
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider){
        $provider_user = Socialite::driver($provider)->user();

        $user = User::where('email', $provider_user->email)->first();

        if ($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $provider_user->id,
            ]);
        } else {
            $user = User::create([
                'provider' => $provider,
                'provider_id' => $provider_user->id,
                'name' => $provider_user->name,
                'email' => $provider_user->email,
                'password' => Hash::make($provider_user->id),
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('home', absolute: false));
    }
}
