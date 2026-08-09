<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('subtitle'),
                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->extraAttributes([
                                'class' => '[&_.fi-fo-rich-editor-header]:!static [&_.fi-fo-rich-editor-header]:!z-0 [&_header]:!static [&_header]:!z-0',
                            ])
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Section::make('📱 Play Store & Script Skin Details')
                            ->description('Atur detail aplikasi/script skin untuk postingan bertipe Play Store Template.')
                            ->visible(fn (Forms\Get $get) => in_array($get('template'), ['playstore', 'app']))
                            ->schema([
                                Forms\Components\TextInput::make('app_poster_35')
                                    ->label('Poster Detail Download (Rasio 3:5)')
                                    ->placeholder('https://images.unsplash.com/photo-...')
                                    ->helperText('URL poster tegak rasio 3:5 untuk section detail unduhan di bawah'),

                                Forms\Components\TextInput::make('app_developer')
                                    ->label('Nama Pengembang / Developer')
                                    ->placeholder('e.g. Moonton / Script MLBB Official')
                                    ->default('Script MLBB'),

                                Forms\Components\TextInput::make('app_version')
                                    ->label('Versi Aplikasi / Script')
                                    ->placeholder('e.g. v1.8.94 (Season 35)')
                                    ->default('Season 41'),

                                Forms\Components\TextInput::make('app_size')
                                    ->label('Ukuran File Download')
                                    ->placeholder('e.g. 15.2 MB')
                                    ->default('10.5 MB'),

                                Forms\Components\TagsInput::make('app_features')
                                    ->label('Tag / Badge Fitur Keunggulan Script (Features Badges)')
                                    ->placeholder('Ketik fitur (cth: Anti-Banned 100%) lalu tekan Enter')
                                    ->default(['Full Effect & Voice', 'Full Backround', 'Work All Mode', 'Work All Grafik', 'No Banned & Bug'])
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('download_links')
                                    ->label('Link Download File (Input Masal / Multi-Server)')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Server / File')
                                            ->placeholder('e.g. Link Utama (MediaFire)')
                                            ->default('Replace')
                                            ->required(),
                                        Forms\Components\TextInput::make('url')
                                            ->label('URL Download')
                                            ->placeholder('https://www.mediafire.com/file/...')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->defaultItems(2)
                                    ->default([
                                        ['name' => 'Replace', 'url' => ''],
                                        ['name' => 'Replace', 'url' => ''],
                                    ])
                                    ->addAction(fn (Forms\Components\Actions\Action $action) => $action->action(function (Forms\Components\Repeater $component): void {
                                        $items = $component->getState() ?? [];
                                        for ($i = 0; $i < 5; $i++) {
                                            $newUuid = $component->generateUuid();
                                            if ($newUuid) {
                                                $items[$newUuid] = ['name' => 'Replace', 'url' => ''];
                                            } else {
                                                $items[] = ['name' => 'Replace', 'url' => ''];
                                            }
                                        }
                                        $component->state($items);
                                        $component->collapsed(false, shouldMakeComponentCollapsible: false);
                                        $component->callAfterStateUpdated();
                                    }))
                                    ->createItemButtonLabel('Tambah 5 Link Download Baru'),
                            ])
                            ->collapsible(),
                    ])->columnSpan(2),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('📌 Pengaturan Artikel & Publikasi')
                            ->schema([
                                Forms\Components\Select::make('template')
                                    ->label('Pilihan Template Postingan')
                                    ->options([
                                        'standard'  => 'Standard Editorial Article (Default)',
                                        'playstore' => 'Google Play Store App Detail Page (App / Mod Script)',
                                    ])
                                    ->default('standard')
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                        // Auto-fill app template defaults when switching to playstore/app
                                        if (in_array($state, ['playstore', 'app']) && ! $record) {
                                            $set('app_developer', 'Script MLBB');
                                            $set('app_version', 'Season 41');
                                            $set('app_size', '10.5 MB');
                                            $set('app_features', ['Full Effect & Voice', 'Full Backround', 'Work All Mode', 'Work All Grafik', 'No Banned & Bug']);
                                            $set('download_links', [
                                                ['name' => 'Replace', 'url' => ''],
                                                ['name' => 'Replace', 'url' => ''],
                                            ]);
                                        }
                                    }),
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required(),
                                Forms\Components\Select::make('author_id')
                                    ->relationship('author', 'name')
                                    ->default(auth()->id()),
                                Forms\Components\TextInput::make('cover_image')
                                    ->label('Thumbnail Utama Artikel (Ratio 16:9 / Header Cover)')
                                    ->placeholder('https://images.unsplash.com/photo-...'),
                                Forms\Components\TextInput::make('read_time')
                                    ->default('5 min read'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('published')
                                    ->required(),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured Article'),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Tanggal Update / Publish')
                                    ->default(now()),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('save')
                                        ->label(fn ($record) => $record ? 'Simpan Perubahan' : 'Buat Artikel')
                                        ->button()
                                        ->color('primary')
                                        ->action(function ($livewire) {
                                            if (method_exists($livewire, 'save')) {
                                                $livewire->save();
                                            } elseif (method_exists($livewire, 'create')) {
                                                $livewire->create();
                                            }
                                        }),
                                    Forms\Components\Actions\Action::make('createAnother')
                                        ->label('Buat & Tambah Lainnya')
                                        ->button()
                                        ->color('gray')
                                        ->visible(fn ($record) => ! $record)
                                        ->action(function ($livewire) {
                                            if (method_exists($livewire, 'create')) {
                                                $livewire->create(another: true);
                                            }
                                        }),
                                    Forms\Components\Actions\Action::make('cancel')
                                        ->label('Batal')
                                        ->button()
                                        ->color('gray')
                                        ->url(fn () => static::getUrl('index')),
                                ])->columnSpanFull(),
                            ]),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul Artikel')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        'archived' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('is_featured')->label('Featured')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')->label('Dipublikasi')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
