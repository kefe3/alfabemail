<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Support\ServiceProvider;

class AdminTheme extends ServiceProvider
{
    public function boot(): void
    {
        Panel::configureUsing(function (Panel $panel) {
            if ($panel->getId() === 'admin') {
                $panel
                    ->colors([
                        'primary' => [
                            50 => '#f0f4ff',
                            100 => '#e0ecff',
                            200 => '#c7ddff',
                            300 => '#a4c7ff',
                            400 => '#818dff',
                            500 => '#5e8df7',
                            600 => '#4c72e5',
                            700 => '#3f5cd3',
                            800 => '#364db8',
                            900 => '#304399',
                            950 => '#1e2b6b',
                        ],
                        'secondary' => [
                            50 => '#fef7ff',
                            100 => '#fce7ff',
                            200 => '#f8d4ff',
                            300 => '#f2b5ff',
                            400 => '#ea8fff',
                            500 => '#dd6eff',
                            600 => '#c444ff',
                            700 => '#a822ff',
                            800 => '#8f0fff',
                            900 => '#7800ff',
                            950 => '#4d00cc',
                        ],
                        'gray' => [
                            50 => '#f8fafc',
                            100 => '#f1f5f9',
                            200 => '#e2e8f0',
                            300 => '#cbd5e1',
                            400 => '#94a3b8',
                            500 => '#64748b',
                            600 => '#475569',
                            700 => '#334155',
                            800 => '#1e293b',
                            900 => '#0f172a',
                            950 => '#020617',
                        ],
                        'success' => [
                            50 => '#f0fdf4',
                            100 => '#dcfce7',
                            200 => '#bbf7d0',
                            300 => '#86efac',
                            400 => '#4ade80',
                            500 => '#22c55e',
                            600 => '#16a34a',
                            700 => '#15803d',
                            800 => '#166534',
                            900 => '#14532d',
                            950 => '#052e16',
                        ],
                        'warning' => [
                            50 => '#fffbeb',
                            100 => '#fef3c7',
                            200 => '#fde68a',
                            300 => '#fcd34d',
                            400 => '#fbbf24',
                            500 => '#f59e0b',
                            600 => '#d97706',
                            700 => '#b45309',
                            800 => '#92400e',
                            900 => '#78350f',
                            950 => '#451a03',
                        ],
                        'danger' => [
                            50 => '#fef2f2',
                            100 => '#fee2e2',
                            200 => '#fecaca',
                            300 => '#fca5a5',
                            400 => '#f87171',
                            500 => '#ef4444',
                            600 => '#dc2626',
                            700 => '#b91c1c',
                            800 => '#991b1b',
                            900 => '#7f1d1d',
                            950 => '#450a0a',
                        ],
                        'info' => [
                            50 => '#f0f9ff',
                            100 => '#e0f2fe',
                            200 => '#bae6fd',
                            300 => '#7dd3fc',
                            400 => '#38bdf8',
                            500 => '#0ea5e9',
                            600 => '#0284c7',
                            700 => '#0369a1',
                            800 => '#075985',
                            900 => '#0c4a6e',
                            950 => '#082f49',
                        ],
                    ])
                    ->font('Inter', url: asset('fonts/inter.css'))
                    ->renderHook(
                        'panels::head.end',
                        fn (): string => '<style>
                            :root {
                                --background: 250 250 250;
                                --foreground: 15 23 42;
                                --card: 255 255 255;
                                --card-foreground: 15 23 42;
                                --popover: 255 255 255;
                                --popover-foreground: 15 23 42;
                                --primary: 94 141 247;
                                --primary-foreground: 255 255 255;
                                --secondary: 221 110 255;
                                --secondary-foreground: 255 255 255;
                                --muted: 243 244 246;
                                --muted-foreground: 100 116 139;
                                --accent: 241 245 249;
                                --accent-foreground: 15 23 42;
                                --destructive: 239 68 68;
                                --destructive-foreground: 255 255 255;
                                --border: 226 232 240;
                                --input: 226 232 240;
                                --ring: 94 141 247;
                                --radius: 0.5rem;
                            }
                            
                            .dark {
                                --background: 15 23 42;
                                --foreground: 250 250 250;
                                --card: 30 41 59;
                                --card-foreground: 250 250 250;
                                --popover: 30 41 59;
                                --popover-foreground: 250 250 250;
                                --primary: 94 141 247;
                                --primary-foreground: 255 255 255;
                                --secondary: 221 110 255;
                                --secondary-foreground: 255 255 255;
                                --muted: 30 41 59;
                                --muted-foreground: 148 163 184;
                                --accent: 30 41 59;
                                --accent-foreground: 250 250 250;
                                --destructive: 239 68 68;
                                --destructive-foreground: 255 255 255;
                                --border: 51 65 85;
                                --input: 51 65 85;
                                --ring: 94 141 247;
                            }
                            
                            /* Soft pastel design system */
                            .filament-panel {
                                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                            }
                            
                            .filament-card {
                                background: rgba(255, 255, 255, 0.9);
                                backdrop-filter: blur(4px);
                                border: 1px solid rgba(226, 232, 240, 0.6);
                                box-shadow: 0 8px 32px rgba(94, 141, 247, 0.08);
                            }
                            
                            .filament-button {
                                border-radius: 999px;
                                font-weight: 600;
                                transition: all 0.2s ease;
                            }
                            
                            .filament-button:hover {
                                transform: translateY(-1px);
                                box-shadow: 0 4px 12px rgba(94, 141, 247, 0.2);
                            }
                            
                            /* Custom scrollbar */
                            ::-webkit-scrollbar {
                                width: 8px;
                                height: 8px;
                            }
                            
                            ::-webkit-scrollbar-track {
                                background: #f1f5f9;
                            }
                            
                            ::-webkit-scrollbar-thumb {
                                background: #cbd5e1;
                                border-radius: 4px;
                            }
                            
                            ::-webkit-scrollbar-thumb:hover {
                                background: #94a3b8;
                            }
                        </style>'
                    );
            }
        });
    }
}
