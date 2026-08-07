<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestArticlesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '📝 Artikel Terbaru & Performa Pembaca';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Article::query()
                    ->with(['category'])
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Thumbnail')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Artikel')
                    ->searchable()
                    ->limit(45)
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-m-eye'),

                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-m-hand-thumb-up'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Article $record): string => route('filament.admin.resources.articles.edit', $record)),
            ]);
    }
}
