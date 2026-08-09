<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    public function getFormActionsAlignment(): Alignment | string
    {
        return Alignment::End;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $this->js("new FilamentNotification().title('Page Saved Successfully').body('Static page updated successfully.').success().send()");
    }
}
