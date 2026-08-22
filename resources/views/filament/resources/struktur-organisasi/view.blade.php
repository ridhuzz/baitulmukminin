<x-filament-panels::page>
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <x-bagan-struktur :blok="$blok" />

        @if (count($relationManagers = $this->getRelationManagers()))
            <x-filament-panels::resources.relation-managers
                :active-manager="$this->activeRelationManager ?? array_key_first($relationManagers)"
                :managers="$relationManagers"
                :owner-record="$record"
                :page-class="static::class"
            />
        @endif
    </div>
</x-filament-panels::page>
