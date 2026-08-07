<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex items-center justify-end pt-4">
            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
                alignment="end"
            />
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
