<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingCommentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '💬 Moderasi Komentar & Masukan Pembaca Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Comment::query()
                    ->with(['article'])
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Nama Pengirim')
                    ->weight('bold')
                    ->description(fn (Comment $record): string => $record->author_email ?? ''),

                Tables\Columns\TextColumn::make('article.title')
                    ->label('Artikel')
                    ->limit(35),

                Tables\Columns\TextColumn::make('content')
                    ->label('Isi Komentar')
                    ->limit(50),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'spam' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->hidden(fn (Comment $record): bool => $record->status === 'approved')
                    ->action(function (Comment $record, $livewire): void {
                        $record->update(['status' => 'approved']);
                        $livewire->js("new FilamentNotification().title('Komentar Disetujui').body('Komentar sekarang dapat dilihat di artikel frontend.').success().send()");
                    }),

                Tables\Actions\Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Comment $record, $livewire): void {
                        $record->delete();
                        $livewire->js("new FilamentNotification().title('Komentar Dihapus').body('Komentar telah berhasil dihapus.').success().send()");
                    }),
            ]);
    }
}
