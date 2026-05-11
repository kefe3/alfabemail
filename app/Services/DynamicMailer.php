<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class DynamicMailer
{
    public function send(Mailable $mailable): void
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasRole('ogrenci')) {
            Mail::send($mailable);
            return;
        }

        $email = $user->email;
        $password = session('ogrenci_password');

        if (!$email || !$password) {
            Mail::send($mailable);
            return;
        }

        Config::set('mail', array_merge(Config::get('mail'), [
            'mailers' => array_merge(Config::get('mail.mailers', []), [
                'ogrenci_smtp' => [
                    'transport' => 'smtp',
                    'host' => 'mail.alfabe.co',
                    'port' => 587,
                    'encryption' => 'tls',
                    'username' => $email,
                    'password' => $password,
                    'timeout' => null,
                    'local_domain' => null,
                ],
            ]),
            'from' => [
                'address' => $email,
                'name' => $user->name,
            ],
        ]));

        Mail::mailer('ogrenci_smtp')->send($mailable);
    }
}