<?php

namespace App\Http\Controllers;

use App\Models\HataBildirisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HataBildirController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad_soyad' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'konu' => 'required|string|max:255',
            'aciklama' => 'required|string|max:5000',
            'ekran_goruntusu' => 'nullable|image|max:5120',
            'sayfa' => 'nullable|string|max:500',
            'tarayici' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('ekran_goruntusu')) {
            $validated['ekran_goruntusu'] = $request->file('ekran_goruntusu')
                ->store('hata-bildirileri', 'public');
        }

        HataBildirisi::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Hata bildiriminiz alındı. Teşekkürler!']);
        }

        return back()->with('success', 'Hata bildiriminiz alındı. Teşekkürler!');
    }
}
