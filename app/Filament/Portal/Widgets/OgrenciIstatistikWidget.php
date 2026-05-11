<?php

namespace App\Filament\Portal\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ogrenci;
use App\Models\MailAktiviteLog;
use Illuminate\Support\Facades\Auth;

class OgrenciIstatistikWidget extends ChartWidget
{
    protected ?string $heading = 'Öğrenci Mail İstatistikleri';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'bayi', 'yonetici', 'ogretmen']) ?? false;
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $query = Ogrenci::query();

        if ($user->hasRole('ogretmen')) {
            $query->whereHas('sinif', fn($q) => $q->where('ogretmen_user_id', $user->id));
        } elseif ($user->hasRole('yonetici')) {
            $query->whereHas('sinif.okul', fn($q) => $q->where('yonetici_user_id', $user->id));
        } elseif ($user->hasRole('bayi')) {
            $query->whereHas('sinif.okul', fn($q) => $q->where('bayi_id', $user->bayi?->id));
        }

        $ogrenciler = $query->with('user')->get();
        $son7gun = now()->subDays(7);

        $gonderilen = MailAktiviteLog::where('tip', 'gonderilen')
            ->where('tarih', '>=', $son7gun)
            ->count();

        $alinan = MailAktiviteLog::where('tip', 'alinan')
            ->where('tarih', '>=', $son7gun)
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Gönderilen',
                    'data' => [$gonderilen],
                    'backgroundColor' => '#7fa7ff',
                ],
                [
                    'label' => 'Alınan',
                    'data' => [$alinan],
                    'backgroundColor' => '#c4ffe7',
                ],
            ],
            'labels' => ['Son 7 Gün'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}