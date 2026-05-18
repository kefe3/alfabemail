<?php

namespace App\Filament\Portal\Resources\Odevler\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Models\Ogrenci;

class OdevlerTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('baslik')
                    ->label('Ödev')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('sinif.ad')
                    ->label('Sınıf')
                    ->sortable(),

                TextColumn::make('ogrenci_sayisi')
                    ->label('Öğrenciler')
                    ->getStateUsing(fn ($record) => $record->ogrenciler()->count()),

                TextColumn::make('teslim_tarihi')
                    ->label('Teslim Tarihi')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tamamlanma_orani')
                    ->label('Tamamlanma')
                    ->getStateUsing(function ($record) {
                        $toplam = $record->ogrenciler()->count();
                        if ($toplam === 0) return '—';
                        $tamamlanan = $record->ogrenciler()->wherePivot('tamamlandi', true)->count();
                        $oran = round(($tamamlanan / $toplam) * 100);
                        return "{$tamamlanan}/{$toplam} (%{$oran})";
                    }),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Action::make('view_ogrenciler')
                    ->label('Öğrenci Durumları')
                    ->icon('heroicon-o-users')
                    ->modalHeading('Ödev Durumları')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat')
                    ->modalContent(function ($record) {
                        $ogrenciler = $record->ogrenciler()
                            ->with('user')
                            ->get()
                            ->map(fn ($o) => [
                                'name' => $o->user?->name ?? $o->mailbox_local_part,
                                'tamamlandi' => $o->pivot->tamamlandi,
                                'tarih' => $o->pivot->tamamlanma_tarihi,
                            ]);

                        $html = '<div class="space-y-2">';
                        foreach ($ogrenciler as $o) {
                            $icon = $o['tamamlandi'] ? '✅' : '⏳';
                            $tarih = $o['tarih'] ? \Carbon\Carbon::parse($o['tarih'])->format('d/m/Y H:i') : '—';
                            $html .= "<div class=\"flex items-center justify-between p-2 bg-gray-50 rounded-lg\">
                                <span>{$icon} {$o['name']}</span>
                                <span class=\"text-sm text-gray-500\">{$tarih}</span>
                            </div>";
                        }
                        $html .= '</div>';

                        return new \Illuminate\Support\HtmlString($html);
                    }),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen'])),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen'])),
                ]),
            ])
            ->headerActions([]);
    }
}
