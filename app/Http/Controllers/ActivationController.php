<?php

namespace App\Http\Controllers;

use App\Models\AktivasyonToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivationController extends Controller
{
    public function activate(string $token): RedirectResponse
    {
        $aktivasyon = AktivasyonToken::where('token', $token)
            ->whereNull('kullanildi_at')
            ->where('expires_at', '>', now())
            ->with('user')
            ->firstOrFail();

        $user = $aktivasyon->user;

        // Token'ı kullanıldı olarak işaretle
        $aktivasyon->update(['kullanildi_at' => now()]);

        // Kullanıcıyı aktif et
        $user->update(['is_active' => true, 'email_verified_at' => now()]);

        Auth::login($user);

        return redirect()->intended(
            $user->hasRole('admin')
                ? route('filament.admin.pages.dashboard')
                : route('filament.portal.pages.dashboard')
        )->with('success', 'Hesabınız başarıyla aktive edildi.');
    }
}
