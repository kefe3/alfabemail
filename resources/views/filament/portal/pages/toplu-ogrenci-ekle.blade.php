<x-filament-panels::page>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-semibold text-blue-800 mb-2">CSV Örnek Format</h3>
            <p class="text-sm text-blue-700 mb-3">Aşağıdaki formatta CSV hazırlayabilirsiniz:</p>
            <code class="block bg-white p-3 rounded text-sm">ad,soyad,veli_email
Ali,Demir,veli@ornek.com
Ayşe,Yılmaz,ayseveli@ornek.com
...</code>
            <a href="/sample-ogrenciler.csv" download class="inline-block mt-3 text-blue-600 hover:underline text-sm">
                ⬇ Örnek CSV İndir
            </a>
        </div>

        <form wire:submit="olustur">
            {{ $this->getSchema('schema') }}

            <div class="mt-6 flex flex-wrap gap-x-3 gap-y-2">
                @foreach ($this->getFormActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        </form>

        <div class="mt-4">
            <a href="{{ request()->fullUrlWithQuery(['more' => 1]) }}" class="text-blue-600 hover:underline">
                + 20 satır daha ekle
            </a>
        </div>

        @if($showResults && !empty($results))
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Sonuçlar</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Öğrenci</th>
                                <th class="px-4 py-2 text-left">E-posta</th>
                                <th class="px-4 py-2 text-left">Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $result['ad'] }}</td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $result['email'] }}</td>
                                    <td class="px-4 py-2">
                                        @if($result['renk'] === 'success')
                                            <span class="text-green-600">✓ {{ $result['durum'] }}</span>
                                        @else
                                            <span class="text-red-600">✗ {{ $result['durum'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>