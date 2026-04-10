<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class VerifyPasswordResetCodeController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session()->has('password_reset_user_id')) {
            return redirect()->route('password.request');
        }

        return view('pages::auth.verify-password-reset-code');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = session('password_reset_user_id');

        if (! $userId) {
            return redirect()->route('password.request')
                ->withErrors(['code' => __('Your session has expired. Please request a new reset code.')]);
        }

        $user = User::query()->find($userId);

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['code' => __('Unable to find your account. Please try again.')]);
        }

        $cached = Cache::get("password_reset_code_{$user->id}");

        if (! $cached || $cached !== $request->code) {
            return back()->withErrors(['code' => __('The code is invalid or has expired.')]);
        }

        Cache::forget("password_reset_code_{$user->id}");
        session()->forget('password_reset_user_id');

        $token = Password::broker()->createToken($user);

        return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email]);
    }
}
