<?php

return [
    App\Providers\AppServiceProvider::class,
    Livewire\LivewireServiceProvider::class,
    BladeUI\Icons\BladeIconsServiceProvider::class,
    BladeUI\Heroicons\BladeHeroiconsServiceProvider::class,
    RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider::class,
    Filament\FilamentServiceProvider::class,
    Filament\Support\SupportServiceProvider::class,
    Filament\Forms\FormsServiceProvider::class,
    Filament\Tables\TablesServiceProvider::class,
    Filament\Actions\ActionsServiceProvider::class,
    Filament\Notifications\NotificationsServiceProvider::class,
    Filament\Infolists\InfolistsServiceProvider::class,
    Filament\Widgets\WidgetsServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
];
