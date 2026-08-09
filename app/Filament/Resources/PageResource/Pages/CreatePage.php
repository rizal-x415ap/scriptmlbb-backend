<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $this->js("new FilamentNotification().title('Page Created Successfully').body('Static page has been published.').success().send()");
    }
}
