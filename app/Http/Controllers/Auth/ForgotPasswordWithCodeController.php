<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordWithCodeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => __('No account found with that email address.')]);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("password_reset_code_{$user->id}", $code, now()->addMinutes(15));

        Mail::to($user->email)->send(new PasswordResetCode($user, $code));

        session(['password_reset_user_id' => $user->id]);

        return redirect()->route('password.code.verify')
            ->with('status', __('A 6-digit reset code has been sent to your email address.'));
    }
}
