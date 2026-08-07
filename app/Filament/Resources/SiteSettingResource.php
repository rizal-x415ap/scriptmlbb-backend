<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationGroup = 'Site Management';

    protected static ?string $navigationLabel = 'Blogger Layout Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('key')
                        ->label('Setting Key Identifier')
                        ->required()
                        ->disabled()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('group')
                        ->label('Setting Category Group')
                        ->disabled()
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('value')
                        ->label('Setting Value (Title, Text, URL, or true/false for Toggles)')
                        ->helperText('For widget switches, use "true" to show or "false" to hide.')
                        ->required()
                        ->rows(3)
                        ->columnSpan(2),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Name')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('value')
                    ->label('Current Setting Value')
                    ->limit(60)
                    ->searchable(),

                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'success',
                        'header' => 'warning',
                        'widgets' => 'info',
                        'footer' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('group')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
        ];
    }
}
