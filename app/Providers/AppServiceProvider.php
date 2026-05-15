<?php

namespace App\Providers;

use App\Models\Okul;
use App\Models\Ogrenci;
use App\Models\Sinif;
use App\Models\User;
use App\Models\Veli;
use App\Observers\ActivityLogObserver;
use Illuminate\Console\Application as Artisan;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        URL::forceScheme('https');

        if (App::environment('production')) {
            Artisan::starting(function ($artisan) {
                $artisan->forbid(FreshCommand::class);
                $artisan->forbid(RefreshCommand::class);
                $artisan->forbid(ResetCommand::class);
                $artisan->forbid(RollbackCommand::class);
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
