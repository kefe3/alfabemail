<?php

namespace App\Http\Controllers;

use App\Models\Veli;
use App\Models\VeliMesaj;
use App\Models\Ogrenci;
use App\Models\User;
use App\Services\MailcowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VeliController extends Controller
{
    public function mesajGonder(Request $request)
    {
        $validated = $request->validate([
            'ogrenci_id' => 'required|exists:ogrenciler,id',
            'kime' => 'required|exists:users,id',
            'konu' => 'required|string|max:255',
            'mesaj' => 'required|string|max:5000',
        ]);

        $veli = Auth::user()->veli;
        if (!$veli) {
            return response()->json(['success' => false, 'message' => 'Veli kaydı bulunamadı.'], 400);
        }

        VeliMesaj::create([
            'veli_id' => $veli->id,
            'ogretmen_user_id' => $validated['kime'],
            'ogrenci_id' => $validated['ogrenci_id'],
            'konu' => $validated['konu'],
            'mesaj' => $validated['mesaj'],
        ]);

        return response()->json(['success' => true, 'message' => 'Mesajınız gönderildi.']);
    }

    public function sifreSifirla(Request $request)
    {
        $validated = $request->validate([
            'ogrenci_id' => 'required|exists:ogrenciler,id',
            'yeni_sifre' => 'required|string|min:6',
            'yeni_sifre_tekrar' => 'required|string|same:yeni_sifre',
            'veli_sifre' => 'required|string',
        ]);

        $veli = Auth::user();
        if (!Hash::check($validated['veli_sifre'], $veli->password)) {
            return response()->json(['success' => false, 'message' => 'Kendi şifreniz doğru değil.'], 400);
        }

        $ogrenci = Ogrenci::findOrFail($validated['ogrenci_id']);

        // Veli bu öğrenciye bağlı mı kontrol et
        if (!$ogrenci->veliler()->where('veli_id', $veli->veli?->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Bu öğrenci size ait değil.'], 403);
        }

        $user = $ogrenci->user;
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Öğrenci kullanıcısı bulunamadı.'], 404);
        }

        $newPassword = $validated['yeni_sifre'];

        // Mailcow şifresini güncelle
        try {
            $mailcow = app(MailcowService::class);
            $mailcow->updateMailboxPassword($user->email, $newPassword);
        } catch (\Exception $e) {
            // Mailcow hatası olsa bile devam et
        }

        // Yerel kullanıcı şifresini güncelle
        $user->password = Hash::make($newPassword);
        $user->save();

        // QR token'daki şifreyi de güncelle
        if ($ogrenci->qr_token) {
            $qrData = json_decode($ogrenci->qr_token, true);
            if ($qrData) {
                $qrData['password'] = $newPassword;
                $ogrenci->qr_token = json_encode($qrData);
                $ogrenci->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Şifre başarıyla sıfırlandı.']);
    }
}
