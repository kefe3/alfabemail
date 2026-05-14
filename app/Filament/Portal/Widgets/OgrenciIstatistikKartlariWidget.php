<?php

namespace App\Filament\Portal\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Ogrenci;
use App\Models\MailAktiviteLog;
use Illuminate\Support\Facades\Auth;
use App\Services\MailcowService;

class OgrenciIstatistikKartlariWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $user = Auth::user();
        $query = Ogrenci::query();

        if ($user->hasRole('ogretmen')) {
            $query->whereHas('sinif', fn($q) => $q->where('ogretmen_user_id', $user->id));
        } elseif ($user->hasRole('yonetici')) {
            $query->whereHas('sinif.okul', fn($q) => $q->where('yonetici_user_id', $user->id));
        }

        $toplamOgrenci = $query->count();
        $aktifOgrenci = $query->whereHas('user', fn($q) => $q->where('is_active', true))->count();

        $son7gun = now()->subDays(7);
        $gonderilen = MailAktiviteLog::where('tip', 'gonderilen')
            ->where('tarih', '>=', $son7gun)
            ->count();
        $alinan = MailAktiviteLog::where('tip', 'alinan')
            ->where('tarih', '>=', $son7gun)
            ->count();

        return [
            Card::make('Toplam Öğrenci', $toplamOgrenci)
                ->description('Sisteme kayıtlı')
                ->icon('heroicon-o-users'),
            Card::make('Aktif Öğrenci', $aktifOgrenci)
                ->description('Giriş yapabilen')
                ->icon('heroicon-o-user-check'),
            Card::make('Gönderilen (7g)', $gonderilen)
                ->description('Son 7 gün')
                ->icon('heroicon-o-paper-airplane')
                ->color('success'),
            Card::make('Alınan (7g)', $alinan)
                ->description('Son 7 gün')
                ->icon('heroicon-o-inbox')
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false;
    }
}