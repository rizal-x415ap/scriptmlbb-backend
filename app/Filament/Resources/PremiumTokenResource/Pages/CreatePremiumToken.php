<?php

namespace App\Filament\Resources\PremiumTokenResource\Pages;

use App\Filament\Resources\PremiumTokenResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePremiumToken extends CreateRecord
{
    protected static string $resource = PremiumTokenResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $this->js("new FilamentNotification().title('Premium Token Created').body('The premium subscription token has been created.').success().send()");
    }
}
