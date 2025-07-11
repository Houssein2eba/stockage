<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }
    public function messages(): array
    {
        return [
            'login.required' => 'Le champ identifiant est obligatoire.',
            'login.string' => 'Le champ identifiant doit être une chaîne de caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $credentials = $this->only('login', 'password');
    if(filter_var($this->input('login'), FILTER_VALIDATE_EMAIL)) {
        $field='email';
    } else {
        $field='number';
    }
    $remember = $this->boolean('remember');

    // Attempt to authenticate the user
    if (Auth::attempt([$field=> $credentials['login'],'password'=>$credentials['password']], $remember)) {
        // If authentication is successful, regenerate the session
        $this->session()->regenerate();

        return;
    }



    // If authentication fails, increment the rate limiter attempts
    RateLimiter::hit($this->throttleKey());

    throw ValidationException::withMessages([
        'login' => trans('auth.failed'),
    ]);
}

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')).'|'.$this->ip());
    }
}
