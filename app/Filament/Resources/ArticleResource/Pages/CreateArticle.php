<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    public function getFormActionsAlignment(): Alignment | string
    {
        return Alignment::End;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $this->js("new FilamentNotification().title('Article Created Successfully').body('The article has been published and is now live on the frontend.').success().send()");
    }
}
