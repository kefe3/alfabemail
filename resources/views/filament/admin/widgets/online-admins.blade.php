<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div style="display:flex;align-items:center;gap:8px;">
                <span>🟢</span>
                <span>Çevrimiçi Adminler</span>
                <span style="background:#10b981;color:white;border-radius:999px;padding:2px 10px;font-size:12px;font-weight:700;">{{ count($onlineAdmins) }}/{{ $totalAdmins }}</span>
            </div>
        </x-slot>

        @if (count($onlineAdmins) > 0)
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
                @foreach ($onlineAdmins as $admin)
                    <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#f0fdf4;border-radius:12px;border:1px solid #bbf7d0;">
                        <span style="width:12px;height:12px;border-radius:50%;background:#10b981;display:inline-block;flex-shrink:0;"></span>
                        <span style="font-weight:600;color:#166534;">{{ $admin->name }}</span>
                        <span style="font-size:11px;color:#6b7280;margin-left:auto;">Aktif</span>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:16px 0;color:#9ca3af;font-size:14px;">
                😴 Çevrimiçi admin bulunmuyor
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
