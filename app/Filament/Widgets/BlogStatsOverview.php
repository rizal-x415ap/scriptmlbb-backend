<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Comment;
use App\Models\PremiumToken;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalArticles = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();

        $totalViews = Article::sum('views_count') ?? 0;
        $totalLikes = Article::sum('likes_count') ?? 0;

        $totalComments = Comment::count();
        $pendingComments = Comment::where('status', 'pending')->count();

        $activeTokens = PremiumToken::where('is_active', true)->count();

        return [
            Stat::make('Total Artikel', number_format($totalArticles))
                ->description("{$publishedArticles} artikel dipublikasikan")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Total Views Pembaca', $this->formatNumber($totalViews))
                ->description($this->formatNumber($totalLikes) . ' total likes pembaca')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Moderasi Komentar', number_format($totalComments))
                ->description($pendingComments > 0 ? "{$pendingComments} komentar perlu disetujui" : 'Semua komentar disetujui')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($pendingComments > 0 ? 'warning' : 'gray'),

            Stat::make('Token Premium Aktif', number_format($activeTokens))
                ->description('Pengguna berlangganan aktif')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info'),
        ];
    }

    private function formatNumber(int $num): string
    {
        if ($num >= 1000000) {
            return round($num / 1000000, 1) . 'M';
        }
        if ($num >= 1000) {
            return round($num / 1000, 1) . 'k';
        }
        return (string) $num;
    }
}
