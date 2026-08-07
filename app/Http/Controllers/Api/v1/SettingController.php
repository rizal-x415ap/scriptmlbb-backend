<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Get all site settings for public visitors
     */
    public function publicIndex(): JsonResponse
    {
        $result = \Illuminate\Support\Facades\Cache::remember('api_site_settings', 600, function () {
            $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

            // Ensure default fallbacks if settings table is clean
            $defaults = [
                'siteTitle' => 'SUPABAZE // Blog & Artikel Software',
                'siteDescription' => 'Blog artikel teknis dan arsitektur pengembangan web modern.',
                'metaKeywords' => 'Vue3, Vite, Laravel, Arsitektur, Web Development',
                'siteBaseUrl' => 'http://localhost:5173',
                'ogImageUrl' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=1200&q=80',
                'robotsTxtContent' => "User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: http://localhost:5173/sitemap.xml",
                'adsTxtContent' => "# Google AdSense Authorization\n# google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0",
                'faviconUrl' => 'https://vitejs.dev/logo.svg',
                'brandLogoText' => 'SUPABAZE',
                'showAnnouncementBar' => 'true',
                'announcementText' => '⚡ DAPATKAN UPDATE ARTIKEL TERBARU DAN WAWASAN ARSITEKTUR SISTEM',
                'announcementLink' => '/archive',
                'featuredPostCategory' => 'All',
                'showMostReadWidget' => 'true',
                'showAuthorWidget' => 'true',
                'showNewsletterWidget' => 'true',
                'showTopicsWidget' => 'true',
                'showFooter' => 'true',
                'footerTagline' => 'KARYA DIGITAL — PRESISI EDITORIAL',
                'footerBio' => 'Menulis tentang arsitektur sistem web, optimasi performa front-end, dan desain pengalaman pengguna yang intuitif.',
                'footerCol2Title' => 'Kategori',
                'footerCol2Link1Text' => 'Rekayasa Web',
                'footerCol2Link1Url' => '/archive?category=Rekayasa Web',
                'footerCol2Link2Text' => 'Arsitektur Sistem',
                'footerCol2Link2Url' => '/archive?category=Arsitektur Sistem',
                'footerCol2Link3Text' => 'Desain Sistem',
                'footerCol2Link3Url' => '/archive?category=Desain Sistem',
                'footerCol2Link4Text' => 'Kecerdasan Buatan',
                'footerCol2Link4Url' => '/archive?category=Kecerdasan Buatan',
                'footerCol3Title' => 'Sumber Daya',
                'footerCol3Link1Text' => 'Dokumentasi Vue 3',
                'footerCol3Link1Url' => 'https://vuejs.org',
                'footerCol3Link2Text' => 'Panduan Vite',
                'footerCol3Link2Url' => 'https://vitejs.dev',
                'footerCol3Link3Text' => 'Dokumentasi Tailwind',
                'footerCol3Link3Url' => 'https://tailwindcss.com',
                'footerCol3Link4Text' => 'Arsip Artikel',
                'footerCol3Link4Url' => '/archive',
                'footerCopyright' => '© 2026 SUPABAZE INC. HAK CIPTA DILINDUNGI UNTUK SELURUH KARYA.',
                'socialLink1Text' => 'Twitter / X',
                'socialLink1Url' => 'https://twitter.com',
                'socialLink2Text' => 'GitHub',
                'socialLink2Url' => 'https://github.com',
                'socialLink3Text' => 'LinkedIn',
                'socialLink3Url' => 'https://linkedin.com',
                'socialLink4Text' => '',
                'socialLink4Url' => '',
                'authorName' => 'Rizal Efendi',
                'authorTitle' => 'Penulis & Pengembang Sistem',
                'authorAvatarUrl' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=250&q=80',
                'authorBio' => 'Berbagi pengalaman teknis seputar pemrograman, arsitektur web, dan desain sistem modern.',
                'authorFollowersCount' => '5.2k Pembaca',
                'authorInstagramUrl' => 'https://instagram.com',
                'authorInstagramHandle' => '@rizal.efendi',
                // Ad Management & Placements Settings
                'showHomeSidebarAd1' => 'true',
                'homeSidebarAd1Script' => '',
                'showHomeSidebarAd2' => 'true',
                'homeSidebarAd2Script' => '',
                'showHomeFeedAd' => 'true',
                'homeFeedAdScript' => '',
                'showPreFooterAd' => 'true',
                'preFooterAdScript' => '',
                'showArticleMiddleAd' => 'true',
                'articleMiddleAdScript' => '',
                'showArticleEndAd' => 'true',
                'articleEndAdScript' => '',
                // Shortener Page Ad Settings
                'showShortenerTopAd' => 'true',
                'shortenerTopAdScript' => '',
                'showShortenerMiddleAd' => 'true',
                'shortenerMiddleAdScript' => '',
                'showShortenerBottomAd' => 'true',
                'shortenerBottomAdScript' => '',
                'showShortenerLeftAd' => 'true',
                'shortenerLeftAdScript' => '',
                'showShortenerRightAd' => 'true',
                'shortenerRightAdScript' => '',
                // Custom Global Header & Body Script Injections (General / Analytics - Always Active)
                'headScriptContent' => '',
                'bodyOpenScriptContent' => '',
                'bodyCloseScriptContent' => '',
                // Custom Global Ad Script Injections (Auto-Disabled for Premium Users)
                'globalHeadAdScript' => '',
                'globalBodyOpenAdScript' => '',
                'globalBodyCloseAdScript' => '',
                // Premium Subscription Settings
                'premiumBuyUrl' => 'https://wa.me/6285262335849?text=Min%20Saya%20mau%20beli%20token%20Script%20MLBB',
                'premiumMonthlyPrice' => '5.000',
            ];

            $res = array_merge($defaults, $settings);

            // Convert string boolean values
            foreach ([
                'showAnnouncementBar', 'showMostReadWidget', 'showAuthorWidget', 'showNewsletterWidget', 'showTopicsWidget', 'showFooter',
                'showHomeSidebarAd1', 'showHomeSidebarAd2', 'showHomeFeedAd', 'showPreFooterAd',
                'showArticleMiddleAd', 'showArticleEndAd', 'showArticleSidebarAd',
                'showShortenerTopAd', 'showShortenerMiddleAd', 'showShortenerBottomAd', 'showShortenerLeftAd', 'showShortenerRightAd'
            ] as $boolKey) {
                if (isset($res[$boolKey])) {
                    $res[$boolKey] = filter_var($res[$boolKey], FILTER_VALIDATE_BOOLEAN);
                }
            }
            return $res;
        });

        return response()->json([
            'status' => 'success',
            'data' => $result
        ])->header('Cache-Control', 'public, max-age=600, s-maxage=600');
    }

    /**
     * Admin Authentication check for layout customizer
     */
    public function adminLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid email or password format.'], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Credentials do not match our records.'], 401);
        }

        // Revoke existing API tokens for security & issue a fresh Sanctum token
        $user->tokens()->delete();
        $token = $user->createToken('admin-customizer-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Admin authenticated successfully.',
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * Update Site Settings from Frontend Customizer
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $payload = $request->except(['_token']);

        foreach ($payload as $key => $value) {
            $group = 'general';
            if (str_contains($key, 'Widget')) $group = 'widgets';
            if (str_contains($key, 'Ad') || str_contains($key, 'Script') || str_contains($key, 'ads')) $group = 'ads';
            if (str_contains($key, 'footer') || str_contains($key, 'Url')) $group = 'footer';
            if (str_contains($key, 'brand') || str_contains($key, 'announcement')) $group = 'header';
            if (str_contains($key, 'meta') || str_contains($key, 'favicon')) $group = 'seo';

            SiteSetting::set($key, $value, $group);
        }

        \Illuminate\Support\Facades\Cache::forget('api_site_settings');

        return response()->json([
            'status' => 'success',
            'message' => 'Site settings saved and published globally!'
        ]);
    }

    /**
     * Plain text response for robots.txt
     */
    public function robotsTxt()
    {
        $default = "User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: http://localhost:5173/sitemap.xml";
        $content = SiteSetting::get('robotsTxtContent', $default);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Plain text response for ads.txt
     */
    public function adsTxt()
    {
        $default = "# Google AdSense Authorization\n# google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0";
        $content = SiteSetting::get('adsTxtContent', $default);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Dynamic XML sitemap generator
     */
    public function sitemapXml()
    {
        $baseUrl = rtrim(SiteSetting::get('siteBaseUrl', 'http://localhost:5173'), '/');
        $articles = \App\Models\Article::where('status', 'published')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $xml .= '<url><loc>' . $baseUrl . '/</loc><priority>1.0</priority><changefreq>daily</changefreq></url>';
        $xml .= '<url><loc>' . $baseUrl . '/archive</loc><priority>0.8</priority><changefreq>daily</changefreq></url>';
        $xml .= '<url><loc>' . $baseUrl . '/about</loc><priority>0.5</priority><changefreq>monthly</changefreq></url>';

        foreach ($articles as $article) {
            $slug = $article->slug ?? $article->id;
            $updatedAt = $article->updated_at ? $article->updated_at->toAtomString() : date('c');
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . '/article/' . $slug . '</loc>';
            $xml .= '<lastmod>' . $updatedAt . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.9</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Dynamic RSS 2.0 XML generator
     */
    public function rssXml()
    {
        $baseUrl = rtrim(SiteSetting::get('siteBaseUrl', 'http://localhost:5173'), '/');
        $siteTitle = SiteSetting::get('siteTitle', 'Script MLBB - Blog & Script Skins');
        $siteDesc = SiteSetting::get('siteDescription', 'Update Script Skin Mobile Legends gratis, tanpa password, dan aman anti-banned.');

        $articles = \App\Models\Article::with(['category', 'author'])
            ->where('status', 'published')
            ->latest('published_at')
            ->take(25)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        $xml .= '<title>' . htmlspecialchars($siteTitle, ENT_XML1, 'UTF-8') . '</title>';
        $xml .= '<link>' . $baseUrl . '</link>';
        $xml .= '<description>' . htmlspecialchars($siteDesc, ENT_XML1, 'UTF-8') . '</description>';
        $xml .= '<language>id-ID</language>';
        $xml .= '<atom:link href="' . $baseUrl . '/rss.xml" rel="self" type="application/rss+xml"/>';

        foreach ($articles as $article) {
            $slug = $article->slug ?? $article->id;
            $link = $baseUrl . '/article/' . $slug;
            $pubDate = $article->published_at ? $article->published_at->toRssString() : date('r');
            $category = $article->category ? $article->category->name : 'Script MLBB';
            $author = $article->author ? $article->author->name : 'Admin';

            $xml .= '<item>';
            $xml .= '<title>' . htmlspecialchars($article->title, ENT_XML1, 'UTF-8') . '</title>';
            $xml .= '<link>' . $link . '</link>';
            $xml .= '<guid isPermaLink="true">' . $link . '</guid>';
            $xml .= '<pubDate>' . $pubDate . '</pubDate>';
            $xml .= '<category>' . htmlspecialchars($category, ENT_XML1, 'UTF-8') . '</category>';
            $xml .= '<author>' . htmlspecialchars($author, ENT_XML1, 'UTF-8') . '</author>';
            $xml .= '<description>' . htmlspecialchars($article->excerpt ?? $article->title, ENT_XML1, 'UTF-8') . '</description>';
            $xml .= '</item>';
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
