<?php

namespace App\Filament\Resources\PremiumTokenResource\Pages;

use App\Filament\Resources\PremiumTokenResource;
use App\Models\PremiumToken;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPremiumTokens extends ListRecords
{
    protected static string $resource = PremiumTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('➕ Buat Token Baru'),
            Actions\Action::make('generateBatch')
                ->label('⚡ Generate 5 Token Sekaligus')
                ->color('amber')
                ->icon('heroicon-o-bolt')
                ->action(function () {
                    for ($i = 0; $i < 5; $i++) {
                        PremiumToken::create([
                            'token' => PremiumToken::generateToken(5),
                            'expires_at' => now()->addDays(30),
                            'is_active' => true,
                        ]);
                    }
                    Notification::make()
                        ->title('5 Token Premium Berhasil Dibuat!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
