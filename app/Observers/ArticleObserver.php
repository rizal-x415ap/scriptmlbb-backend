<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\ShortLink;

class ArticleObserver
{
    /**
     * Handle the Article "saved" event.
     */
    public function saved(Article $article): void
    {
        $this->syncShortLinks($article);
    }

    /**
     * Synchronize short links for the article's download links.
     */
    protected function syncShortLinks(Article $article): void
    {
        $downloadLinks = $article->download_links;

        if (empty($downloadLinks) || !is_array($downloadLinks)) {
            // Also check app_download_url
            if (!empty($article->app_download_url)) {
                $downloadLinks = [
                    ['name' => 'Main Download Link', 'url' => $article->app_download_url]
                ];
            } else {
                return;
            }
        }

        foreach ($downloadLinks as $link) {
            $url = is_array($link) ? ($link['url'] ?? null) : null;
            $name = is_array($link) ? ($link['name'] ?? 'Download File') : 'Download File';

            if (!$url) {
                continue;
            }

            // Check if short link already exists for this article & URL
            $exists = ShortLink::where('article_id', $article->id)
                ->where('original_url', $url)
                ->exists();

            if (!$exists) {
                ShortLink::create([
                    'code' => ShortLink::generateUniqueCode(8),
                    'article_id' => $article->id,
                    'original_url' => $url,
                    'link_name' => $name,
                    'clicks_count' => 0,
                ]);
            }
        }
    }
}
