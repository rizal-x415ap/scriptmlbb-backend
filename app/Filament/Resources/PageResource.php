<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?string $navigationLabel = 'Halaman Statis (Pages)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Halaman')
                            ->placeholder('e.g. Tentang Kami / Kebijakan Privasi')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug Halaman')
                            ->placeholder('e.g. tentang-kami / privacy-policy')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Deskripsi Ringkas (Meta Description for SEO)')
                            ->rows(2)
                            ->placeholder('Deskripsi ringkas halaman untuk mesin pencarian...'),

                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Konten Halaman')
                            ->required()
                            ->columnSpanFull(),
                    ])->columnSpan(2),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('📌 Status & Publikasi')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status Publikasi')
                                    ->options([
                                        'draft' => 'Draft (Disembunyikan)',
                                        'published' => 'Published (Tampil Publik)',
                                    ])
                                    ->default('published')
                                    ->required(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('save')
                                        ->label(fn ($record) => $record ? '💾 Simpan Perubahan Halaman' : '🚀 Publish Halaman Baru')
                                        ->button()
                                        ->color('primary')
                                        ->extraAttributes(['class' => 'w-full justify-center'])
                                        ->action(function ($livewire) {
                                            if (method_exists($livewire, 'save')) {
                                                $livewire->save();
                                            } elseif (method_exists($livewire, 'create')) {
                                                $livewire->create();
                                            }
                                        }),
                                ])->fullWidth(),
                            ]),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul Halaman')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('URL Slug')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                    }),
                Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui Pada')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
