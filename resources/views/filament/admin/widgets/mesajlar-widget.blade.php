<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
    <div class="flex justify-between items-center mb-3">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Mesajlar</h3>
        @if($okunmamis > 0)
            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $okunmamis }} yeni</span>
        @endif
    </div>
    
    @if($son_mesajlar->count() > 0)
        <div class="space-y-3">
            @foreach($son_mesajlar as $mesaj)
                <div class="p-3 rounded-lg {{ $mesaj->okundu ? 'bg-gray-50' : 'bg-blue-50' }} dark:bg-gray-700">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $mesaj->user?->name ?? 'Bilinmiyor' }}</span>
                        <span class="text-xs text-gray-500">{{ $mesaj->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ Str::limit($mesaj->mesaj, 100) }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 text-sm">Henüz mesaj yok.</p>
    @endif
</div>