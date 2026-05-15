<?php

namespace App\Providers;

use App\Console\Commands\ForbiddenCommand;
use App\Models\Okul;
use App\Models\Ogrenci;
use App\Models\Sinif;
use App\Models\User;
use App\Models\Veli;
use App\Observers\ActivityLogObserver;
use Illuminate\Console\Application as Artisan;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (App::environment('production')) {
            Artisan::starting(function (Artisan $artisan) {
                $forbidden = [
                    'migrate:fresh',
                    'migrate:refresh',
                    'migrate:reset',
                    'migrate:rollback',
                    'db:wipe',
                ];

                foreach ($forbidden as $name) {
                    if ($artisan->has($name)) {
                        $artisan->add(new ForbiddenCommand($name));
                    }
                }
            });
        }
    }

    public function boot(): void
    {
        URL::forceScheme('https');

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
