<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function authenticate(): void
    {
        $email = Str::lower($this->string('email')->toString());
        $user = User::query()->where('email', $email)->first();

        if ($user !== null && ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Your account is inactive. Please contact an administrator.',
            ]);
        }

        if ($user?->final_login_attempt_available_at?->isFuture()) {
            $this->session()->flash('final_login_attempt_available_at', $user->final_login_attempt_available_at->toIso8601String());

            throw ValidationException::withMessages([
                'email' => 'You have one final login attempt after the 1-minute countdown.',
            ]);
        }

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            if ($user !== null) {
                $this->recordFailedAttempt($user);
            }

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user?->resetLoginAttempts();
        RateLimiter::clear($this->throttleKey());
    }

    private function recordFailedAttempt(User $user): void
    {
        if ($user->failed_login_attempts >= 3 && $user->final_login_attempt_available_at?->isPast()) {
            $user->forceFill([
                'is_active' => false,
                'final_login_attempt_available_at' => null,
            ])->save();

            throw ValidationException::withMessages([
                'email' => 'Your final login attempt was incorrect. Your account has been deactivated; contact an administrator.',
            ]);
        }

        $attempts = $user->failed_login_attempts + 1;
        $updates = ['failed_login_attempts' => $attempts];

        if ($attempts === 3) {
            $finalAttemptAt = now()->addMinute();
            $updates['final_login_attempt_available_at'] = $finalAttemptAt;
            $this->session()->flash('final_login_attempt_available_at', $finalAttemptAt->toIso8601String());
        }

        $user->forceFill($updates)->save();
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
