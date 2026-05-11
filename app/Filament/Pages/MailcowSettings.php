<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\MailcowService;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class MailcowSettings extends Page
{
    protected static bool $shouldRegisterNavigation = true;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $title = 'Mailcow API Ayarları';
    protected static ?string $navigationLabel = 'Mailcow Ayarları';
    protected string $view = 'filament.pages.mailcow-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'mailcow_api_base_url' => Setting::where('key', 'mailcow_api_base_url')->first()?->value ?? config('mailcow.api_base_url'),
            'mailcow_api_key' => '••••••••••••••••',
            'mailcow_domain' => Setting::where('key', 'mailcow_domain')->first()?->value ?? config('mailcow.domain'),
            'mailcow_default_quota_mb' => Setting::where('key', 'mailcow_default_quota_mb')->first()?->value ?? config('mailcow.default_quota_mb'),
        ];
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mailcow Bağlantı Bilgileri')
                    ->description('Bu ayarlar salt okunurdur. Değişiklik için yazılımcı ile iletişime geçin.')
                    ->schema([
                        TextInput::make('mailcow_api_base_url')
                            ->label('API Base URL')
                            ->disabled()
                            ->helperText('Değiştirilemez'),
                        TextInput::make('mailcow_api_key')
                            ->label('API Anahtarı (X-API-Key)')
                            ->disabled()
                            ->helperText('Gizli - Değiştirilemez'),
                        TextInput::make('mailcow_domain')
                            ->label('E-Posta Domaini')
                            ->disabled()
                            ->helperText('Değiştirilemez'),
                        TextInput::make('mailcow_default_quota_mb')
                            ->label('Varsayılan Kota (MB)')
                            ->disabled()
                            ->helperText('Değiştirilemez'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('test_connection')
                ->label('Bağlantıyı Test Et')
                ->color('success')
                ->action('testConnection'),
        ];
    }

    public function save(): void
    {
        $data = $this->getSchema('schema')->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            Cache::forget("setting_{$key}");
        }

        app(MailcowService::class)->refreshConfig();

        Notification::make()
            ->title('Ayarlar başarıyla kaydedildi.')
            ->success()
            ->send();
    }

    public function testConnection(): void
    {
        $mailcow = app(MailcowService::class);
        
        if ($mailcow->testConnection()) {
            Notification::make()
                ->title('✅ Mailcow bağlantısı başarılı!')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('❌ Bağlantı başarısız.')
                ->body('Lütfen API ayarlarını kontrol edin.')
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('admin');
    }
}
