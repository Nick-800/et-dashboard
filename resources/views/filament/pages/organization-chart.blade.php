<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Save Organization Chart
            </x-filament::button>
        </div>
    </form>

    @if($this->getOrganizationChart())
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Current Organization Chart
            </x-slot>

            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $this->getOrganizationChart()) }}"
                     alt="Organization Chart"
                     class="max-w-full h-auto rounded-lg shadow-lg">
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
