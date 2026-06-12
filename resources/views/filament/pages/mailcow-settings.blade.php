<x-filament-panels::page>
    <form>
        {{ $this->getSchema("schema") }}

        <div class="mt-6 flex flex-wrap gap-x-3 gap-y-2">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament-panels::page>
