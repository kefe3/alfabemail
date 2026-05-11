<?php

namespace App\Filament\Resources\Okuls\Pages\OkulOnay;

use App\Models\Okul;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListPendingOkullar extends ListRecords implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = \App\Filament\Portal\Resources\Okuls\OkulResource::class;

    protected static ?string $title = 'Okul Onayları';
    protected static ?string $navigationLabel = 'Okul Onayları';
    protected static bool $shouldRegisterNavigation = true;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function getHeading(): string
    {
        return 'Onay Bekleyen Okullar';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Okul::where('durum', 'beklemede'))
            ->columns([
                TextColumn::make('ad')
                    ->label('Okul Adı')
                    ->searchable(),
                TextColumn::make('bayi.user.name')
                    ->label('Bayi')
                    ->searchable(),
                TextColumn::make('yonetici_ad_soyad')
                    ->label('Yönetici')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Başvuru Tarihi')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Action::make('onayla')
                    ->label('Onayla')
                    ->color('success')
                    ->action(function (Okul $record) {
                        $record->update(['durum' => 'onayli']);
                        Notification::make()
                            ->title('Başarılı')
                            ->body('Okul onaylandı.')
                            ->success()
                            ->send();
                    }),
                Action::make('reddet')
                    ->label('Reddet')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Okul $record) {
                        $record->update(['durum' => 'reddet']);
                        Notification::make()
                            ->title('Başarılı')
                            ->body('Okul reddedildi.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}