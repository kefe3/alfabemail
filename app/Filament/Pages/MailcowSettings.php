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
        $settings = Setting::where('key', 'like', 'mailcow_%')->get()->pluck('value', 'key');
        
        $this->data = [
            'mailcow_api_base_url' => $settings->get('mailcow_api_base_url', config('mailcow.api_base_url', '')),
            'mailcow_api_key' => $settings->get('mailcow_api_key', config('mailcow.api_key', '')),
            'mailcow_domain' => $settings->get('mailcow_domain', config('mailcow.domain', '')),
            'mailcow_default_quota_mb' => $settings->get('mailcow_default_quota_mb', config('mailcow.default_quota_mb', '100')),
        ];
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mailcow Bağlantı Bilgileri')
                    ->schema([
                        TextInput::make('mailcow_api_base_url')
                            ->label('API Base URL')
                            ->placeholder('https://mail.alfabe.co')
                            ->helperText('Mailcow API URL (örn: https://mail.alfabe.co)'),
                        TextInput::make('mailcow_api_key')
                            ->label('API Anahtarı (X-API-Key)')
                            ->password()
                            ->revealable()
                            ->helperText('Mailcow admin panelinden aldığınız API key'),
                        TextInput::make('mailcow_domain')
                            ->label('E-Posta Domaini')
                            ->placeholder('alfabe.co')
                            ->helperText('Kullanılan e-posta domaini'),
                        TextInput::make('mailcow_default_quota_mb')
                            ->label('Varsayılan Kota (MB)')
                            ->numeric()
                            ->placeholder('100')
                            ->helperText('Yeni oluşturulan mailboxlar için varsayılan kota'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->submit('save'),
            Action::make('test_connection')
                ->label('Bağlantıyı Test Et')
                ->color('success')
                ->action('testConnection'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
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
