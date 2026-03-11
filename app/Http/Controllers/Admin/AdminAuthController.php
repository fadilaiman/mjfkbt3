<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Models\AdminLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    use LogsAdminAction;

    public function showLogin(): Response
    {
        return Inertia::render('Admin/Login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $maxAttempts = config('mjfkbt3.admin.max_login_attempts', 5);
        $decayMinutes = config('mjfkbt3.admin.login_decay_minutes', 15);
        $throttleKey = 'admin-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => [
                    "Terlalu banyak percubaan log masuk. Sila cuba lagi dalam {$seconds} saat.",
                ],
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, $decayMinutes * 60);

            throw ValidationException::withMessages([
                'email' => ['E-mel atau kata laluan tidak sah.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        $admin = Auth::guard('admin')->user();
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        AdminLog::create([
            'admin_user_id' => $admin->id,
            'action' => 'login',
            'description' => 'Admin log masuk berjaya',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
