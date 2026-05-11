<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class RegistrationChart extends ChartWidget
{
    protected ?string $heading = 'Yeni Kayıtlar (Son 30 Gün)';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    protected function getData(): array
    {
        // Not: 'flowframe/laravel-trend' paketi yüklü olmayabilir. 
        // Basit bir Eloquent sorgusu ile son 7 günü alalım.
        
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');
            $data[] = User::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Yeni Kullanıcılar',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#7fa7ff',
                    'backgroundColor' => 'rgba(127, 167, 255, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
