<?php

namespace App\Http\Controllers;

use App\Models\Ogrenci;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OgrenciController extends Controller
{
    public function qrLogin(Request $request): JsonResponse
    {
        $request->validate(['qr_token' => 'required|string']);

        $ogrenci = Ogrenci::where('qr_token', $request->qr_token)
            ->with('user')
            ->first();

        if (!$ogrenci || !$ogrenci->user || !$ogrenci->user->is_active) {
            return response()->json(['message' => 'Geçersiz veya süresi dolmuş karekod.'], 401);
        }

        Auth::login($ogrenci->user);

        return response()->json(['redirect' => route('home')]);
    }
}
