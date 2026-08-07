<?php

namespace App\Filament\Resources\PremiumTokenResource\Pages;

use App\Filament\Resources\PremiumTokenResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPremiumToken extends EditRecord
{
    protected static string $resource = PremiumTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $this->js("new FilamentNotification().title('Premium Token Updated').body('The premium subscription token details updated.').success().send()");
    }
}
