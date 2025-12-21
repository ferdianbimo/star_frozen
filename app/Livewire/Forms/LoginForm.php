<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * LoginForm - Livewire Form untuk proses autentikasi user.
 *
 * Class ini menangani form login dengan fitur:
 * - Validasi email dan password
 * - Remember me functionality
 * - Rate limiting untuk mencegah brute force
 * - Throttle key berbasis email + IP
 *
 * @package App\Livewire\Forms
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property string $email    Email user untuk login
 * @property string $password Password user
 * @property bool   $remember Flag untuk "remember me"
 */
class LoginForm extends Form
{
    /**
     * Alamat email user untuk login.
     *
     * @var string
     */
    #[Validate('required|string|email')]
    public string $email = '';

    /**
     * Password user untuk login.
     *
     * @var string
     */
    #[Validate('required|string')]
    public string $password = '';

    /**
     * Flag untuk menyimpan session login.
     *
     * @var bool
     */
    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Mencoba autentikasi dengan credentials yang diberikan.
     *
     * Proses:
     * 1. Cek rate limiting
     * 2. Attempt login dengan email/password
     * 3. Jika gagal, increment rate limiter
     * 4. Jika sukses, clear rate limiter
     *
     * @return void
     * @throws ValidationException Jika login gagal atau rate limited
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Memastikan request tidak melebihi rate limit.
     *
     * Maksimum 5 percobaan login gagal. Jika melebihi,
     * user harus menunggu sesuai waktu cooldown.
     *
     * @return void
     * @throws ValidationException Jika rate limited
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Generate throttle key untuk rate limiting.
     *
     * Key dibuat dari kombinasi email (lowercase) dan IP address
     * untuk membatasi percobaan login per kombinasi user+device.
     *
     * @return string Format: "email|ip_address"
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
