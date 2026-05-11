<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Okul;
use App\Models\Sinif;
use App\Models\Ogrenci;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortalStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $user = auth()->user();
        
        if (!$user) {
            return [];
        }

        $stats = [];

        if ($user->hasRole('yonetici')) {
            $okulId = $user->okul?->id;
            
            if (!$okulId) {
                $stats[] = Stat::make('Uyarı', 'Okul bulunamadı')
                    ->description('Bu hesaba bağlı okul yok')
                    ->color('danger');
            } else {
                $ogretmenSayisi = \DB::table('ogretmen_sinif')
                    ->whereIn('sinif_id', Sinif::where('okul_id', $okulId)->pluck('id'))
                    ->distinct('ogretmen_user_id')
                    ->count();
                
                $stats[] = Stat::make('Öğretmenler', $ogretmenSayisi)
                    ->description('Okulunuzdaki aktif öğretmenler')
                    ->color('primary');

                $stats[] = Stat::make('Sınıflar', Sinif::where('okul_id', $okulId)->count())
                    ->color('info');

                $stats[] = Stat::make('Öğrenciler', Ogrenci::whereHas('sinif', fn($q) => $q->where('okul_id', $okulId))->count())
                    ->color('success');
            }
        }

        if ($user->hasRole('ogretmen')) {
            $sinifIds = DB::table('ogretmen_sinif')
                ->where('ogretmen_user_id', $user->id)
                ->pluck('sinif_id');
            
            $stats[] = Stat::make('Sınıflarım', $sinifIds->count())
                ->color('primary');

            $stats[] = Stat::make('Öğrencilerim', Ogrenci::whereIn('sinif_id', $sinifIds)->count())
                ->description('Mailbox sahibi öğrencileriniz')
                ->color('success');
        }

        return $stats;
    }
}
