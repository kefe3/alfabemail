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
        return [
            'datasets' => [
                [
                    'label' => 'Gönderilen Mailler',
                    'data' => [0, 0, 0, 0, 0, 0, 0],
                    'borderColor' => '#7fa7ff',
                ],
                [
                    'label' => 'Alınan Mailler',
                    'data' => [0, 0, 0, 0, 0, 0, 0],
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
