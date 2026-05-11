<?php

namespace App\Filament\Portal\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ogrenci;

class OgrenciAktiviteWidget extends ChartWidget
{
    protected ?string $heading = 'Öğrenci eMail Etkinliği';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('veli') ?? false;
    }

    protected function getData(): array
    {
        // Gerçek veriler Mailcow API veya yerel loglardan gelmeli
        // Şimdilik demo verisi:
        return [
            'datasets' => [
                [
                    'label' => 'Gönderilen Mailler',
                    'data' => [5, 12, 8, 15, 10, 20, 14],
                    'borderColor' => '#7fa7ff',
                ],
                [
                    'label' => 'Alınan Mailler',
                    'data' => [10, 15, 12, 20, 18, 25, 22],
                    'borderColor' => '#c4ffe7',
                ],
            ],
            'labels' => ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
