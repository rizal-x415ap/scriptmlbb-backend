<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Moderation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('author_name')->required(),
                Forms\Components\TextInput::make('author_email')->required()->email(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'spam' => 'Spam',
                    ])
                    ->default('approved')
                    ->required(),
                Forms\Components\Textarea::make('content')->required()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Nama Pengirim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author_email')
                    ->label('Email Pengirim')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope')
                    ->copyable(),
                Tables\Columns\TextColumn::make('article.title')
                    ->label('Artikel')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('content')
                    ->label('Isi Komentar')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_author_reply')
                    ->label('Balasan Admin')
                    ->boolean(),
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
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-m-chat-bubble-bottom-center-text')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('reply_content')
                            ->label('Admin Reply Content')
                            ->placeholder('Write your reply as Admin/Author...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Comment $record, array $data, $livewire): void {
                        $cleanText = strip_tags($data['reply_content']);
                        $cleanText = preg_replace('/https?:\/\/\S+/i', '', $cleanText);

                        $admin = auth()->user();
                        $adminName = $admin?->name ?: 'Admin';
                        $authorName = str_ends_with($adminName, ' (Author)') ? $adminName : sprintf('%s (Author)', $adminName);
                        $adminEmail = $admin?->email ?: 'admin@supabaze.com';

                        Comment::create([
                            'article_id' => $record->article_id,
                            'parent_id' => $record->id,
                            'author_name' => $authorName,
                            'author_email' => $adminEmail,
                            'content' => $cleanText,
                            'status' => 'approved',
                            'is_author_reply' => true,
                        ]);

                        $livewire->js("new FilamentNotification().title('Reply Published').body('Admin reply has been posted directly to the frontend article thread!').success().send()");
                    }),
                Tables\Actions\EditAction::make()
                    ->successNotification(null)
                    ->after(function ($livewire) {
                        $livewire->js("new FilamentNotification().title('Comment Updated').body('Comment details updated successfully.').success().send()");
                    }),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(null)
                    ->after(function ($livewire) {
                        $livewire->js("new FilamentNotification().title('Comment Deleted').body('Comment deleted successfully.').success().send()");
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
        ];
    }
}
