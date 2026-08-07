<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PremiumTokenResource\Pages;
use App\Models\PremiumToken;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PremiumTokenResource extends Resource
{
    protected static ?string $model = PremiumToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Site Management';

    protected static ?string $navigationLabel = 'Tokens Premium';

    protected static ?string $title = 'Premium Tokens Subscription';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Token Premium')
                    ->schema([
                        Forms\Components\TextInput::make('token')
                            ->label('Kode Token (5 Huruf Besar)')
                            ->default(fn () => PremiumToken::generateToken(5))
                            ->required()
                            ->maxLength(5)
                            ->minLength(5)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase; font-family: monospace; font-weight: bold; font-size: 1.25rem;']),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Tanggal Kedaluwarsa (Expired Date)')
                            ->default(now()->addDays(30))
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),

                        Forms\Components\TextInput::make('device_name')
                            ->label('Perangkat Terhubung (Device Name)')
                            ->placeholder('Belum diaktifkan')
                            ->disabled(),

                        Forms\Components\TextInput::make('device_fingerprint')
                            ->label('Device Fingerprint ID')
                            ->placeholder('Belum terikat perangkat')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('activated_at')
                            ->label('Tanggal Aktivasi')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('token')
                    ->label('Kode Token')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('warning')
                    ->extraAttributes(['class' => 'font-mono text-base font-bold']),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('device_name')
                    ->label('Perangkat')
                    ->default('Belum Diaktifkan')
                    ->badge()
                    ->color(fn ($record) => $record->device_fingerprint ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('activated_at')
                    ->label('Diaktifkan Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Belum Diaktifkan'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Kedaluwarsa')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && now()->greaterThan($record->expires_at) ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Token'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPremiumTokens::route('/'),
            'create' => Pages\CreatePremiumToken::route('/create'),
            'edit' => Pages\EditPremiumToken::route('/{record}/edit'),
        ];
    }
}
