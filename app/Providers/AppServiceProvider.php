<?php

namespace App\Providers;

use App\Models\Okul;
use App\Models\Ogrenci;
use App\Models\Sinif;
use App\Models\User;
use App\Models\Veli;
use App\Observers\ActivityLogObserver;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $forbiddenCommands = [
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'migrate:rollback',
        'db:wipe',
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        URL::forceScheme('https');

        if (App::environment('production')) {
            Event::listen(CommandStarting::class, function (CommandStarting $event): void {
                if (in_array($event->command, $this->forbiddenCommands, true)) {
                    throw new \RuntimeException(
                        "`{$event->command}` komutu production ortamında çalıştırılamaz!"
                    );
                }
            });
        }

        Okul::observe(ActivityLogObserver::class);
        Sinif::observe(ActivityLogObserver::class);
        Ogrenci::observe(ActivityLogObserver::class);
        User::observe(ActivityLogObserver::class);
        Veli::observe(ActivityLogObserver::class);

        Event::listen('Illuminate\Auth\Events\Login', function ($event) {
            \App\Models\ActivityLog::create([
                'user_id' => $event->user?->id,
                'user_name' => $event->user?->name ?? $event->user?->email,
                'user_role' => $event->user?->roles->first()?->name ?? 'system',
                'action' => 'login',
                'module' => 'auth',
                'description' => 'Giriş yaptı',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        Event::listen('Illuminate\Auth\Events\Logout', function ($event) {
            \App\Models\ActivityLog::create([
                'user_id' => $event->user?->id,
                'user_name' => $event->user?->name ?? $event->user?->email,
                'user_role' => $event->user?->roles->first()?->name ?? 'system',
                'action' => 'logout',
                'module' => 'auth',
                'description' => 'Çıkış yaptı',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
