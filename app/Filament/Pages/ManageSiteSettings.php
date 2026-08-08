<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSiteSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationGroup = 'Site Management';

    protected static ?string $navigationLabel = 'Blogger Site Settings';

    protected static ?string $title = 'Blogger Site & Layout Settings';

    protected static string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Load settings from database or fallback defaults
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        $defaults = [
            'siteTitle' => 'SUPABAZE // Editorial & Tech',
            'siteDescription' => 'High-density technical blog and engineering design system.',
            'metaKeywords' => 'Vue3, Vite, Laravel, Architecture, TailwindCSS',
            'faviconUrl' => '/favicon.svg',
            'brandLogoText' => 'SUPABAZE',
            'showAnnouncementBar' => true,
            'announcementText' => '⚡ TAILWIND V4 & VUE 3 ARCHITECTURE DISPATCH IS NOW LIVE',
            'announcementLink' => '/article/tailwind-css-v4-rethinking-utility-engine-performance',
            'showMostReadWidget' => true,
            'showAuthorWidget' => true,
            'showNewsletterWidget' => true,
            'showTopicsWidget' => true,
            'showHomeSidebarAd1' => true,
            'homeSidebarAd1Script' => '',
            'showHomeSidebarAd2' => true,
            'homeSidebarAd2Script' => '',
            'showHomeFeedAd' => true,
            'homeFeedAdScript' => '',
            'showPreFooterAd' => true,
            'preFooterAdScript' => '',
            'showArticleMiddleAd' => true,
            'articleMiddleAdScript' => '',
            'showArticleEndAd' => true,
            'articleEndAdScript' => '',
            'showArticleSidebarAd' => true,
            'articleSidebarAdScript' => '',
            'headScriptContent' => '',
            'bodyOpenScriptContent' => '',
            'bodyCloseScriptContent' => '',
            'archiveTitle' => 'Jelajahi Arsip Artikel & Catatan Teknis',
            'archiveSubtitle' => 'Filter seluruh koleksi artikel teknis, catatan arsitektur sistem, dan panduan teknis modern.',
            'footerCopyright' => '© 2026 SUPABAZE INC. ALL RIGHTS RESERVED.',
            'footerBio' => 'Writing about high-throughput web systems, front-end performance tooling, and anti-slop user experience designs.',
            'twitterUrl' => 'https://twitter.com',
            'githubUrl' => 'https://github.com',
            'linkedinUrl' => 'https://linkedin.com',
            'contactEmail' => 'rizal@scriptmlbb.com',
            'premiumFreeAdUrl' => 'https://scriptmlbb.com',
        ];

        $merged = array_merge($defaults, $settings);

        // Cast boolean toggles
        foreach ([
            'showAnnouncementBar', 'showMostReadWidget', 'showAuthorWidget', 'showNewsletterWidget', 'showTopicsWidget',
            'showHomeSidebarAd1', 'showHomeSidebarAd2', 'showHomeFeedAd', 'showPreFooterAd',
            'showArticleMiddleAd', 'showArticleEndAd', 'showArticleSidebarAd'
        ] as $boolKey) {
            if (isset($merged[$boolKey])) {
                $merged[$boolKey] = filter_var($merged[$boolKey], FILTER_VALIDATE_BOOLEAN);
            }
        }

        $this->form->fill($merged);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        // TAB 1: GENERAL & SEO
                        Forms\Components\Tabs\Tab::make('⚙️ General & SEO')
                            ->schema([
                                Forms\Components\TextInput::make('siteTitle')
                                    ->label('Site Title (Document Title)')
                                    ->placeholder('e.g. SUPABAZE // Editorial & Tech')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('siteDescription')
                                    ->label('Site Description (Meta SEO)')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('siteBaseUrl')
                                    ->label('Site Base Canonical URL (Production Domain)')
                                    ->placeholder('e.g. https://supabaze.com or http://localhost:5173')
                                    ->helperText('Used to generate dynamic SEO Canonical <link rel="canonical"> tags across all pages.')
                                    ->url()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('contactEmail')
                                    ->label('Target Email Form Kontak & Validasi')
                                    ->placeholder('rizal@scriptmlbb.com')
                                    ->helperText('Alamat email penerima pesan FormSubmit & kontak manual.')
                                    ->email()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('metaKeywords')
                                    ->label('Meta Keywords')
                                    ->placeholder('Vue3, Vite, Laravel, Architecture')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('faviconUrl')
                                    ->label('Browser Tab Favicon Icon URL')
                                    ->placeholder('/favicon.svg')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('ogImageUrl')
                                    ->label('Default Open Graph Image URL (og:image / twitter:image)')
                                    ->placeholder('https://images.unsplash.com/photo-...')
                                    ->helperText('Default social sharing banner image when sharing the homepage on Twitter/Facebook/LinkedIn.')
                                    ->url()
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('🤖 Crawler & Ad Authorization (robots.txt & ads.txt)')
                                    ->description('Customize plain text content for search engine crawlers and Google AdSense authorization')
                                    ->schema([
                                        Forms\Components\Textarea::make('robotsTxtContent')
                                            ->label('robots.txt Content')
                                            ->helperText('Accessible at http://127.0.0.1:8000/robots.txt and http://localhost:5173/robots.txt')
                                            ->rows(5)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('adsTxtContent')
                                            ->label('ads.txt Content (Google AdSense Authorization)')
                                            ->helperText('Accessible at http://127.0.0.1:8000/ads.txt and http://localhost:5173/ads.txt')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 2: HEADER & BRANDING
                        Forms\Components\Tabs\Tab::make('🎨 Header & Branding')
                            ->schema([
                                Forms\Components\TextInput::make('brandLogoText')
                                    ->label('Brand Logo Name / Title')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('brandLogoUrl')
                                    ->label('Brand Logo Image URL (Gambar Logo Opsional)')
                                    ->placeholder('https://example.com/logo.png atau /favicon.svg')
                                    ->helperText('Jika diisi, gambar ini akan digunakan sebagai Logo pada Header & Footer menggantikan kotak huruf bawaan.')
                                    ->columnSpanFull(),

                                Forms\Components\Toggle::make('showAnnouncementBar')
                                    ->label('Show Top Announcement Bar')
                                    ->default(true),

                                Forms\Components\TextInput::make('announcementText')
                                    ->label('Announcement Bar Text')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('announcementLink')
                                    ->label('Announcement Action Link')
                                    ->columnSpanFull(),
                            ]),

                        // TAB 3: WIDGETS & LAYOUT
                        Forms\Components\Tabs\Tab::make('🧩 Widgets & Layout')
                            ->schema([
                                Forms\Components\Section::make('Featured Posts Section Category')
                                    ->description('Select 1 specific category to display in the Featured Posts section on the homepage.')
                                    ->schema([
                                        Forms\Components\Select::make('featuredPostCategory')
                                            ->label('Featured Posts Category')
                                            ->options(function () {
                                                $cats = ['All' => 'All Categories (Default)'];
                                                try {
                                                    $dbCats = Category::pluck('name', 'name')->toArray();
                                                    return array_merge($cats, $dbCats);
                                                } catch (\Throwable $e) {
                                                    return $cats;
                                                }
                                            })
                                            ->default('All')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Right Sidebar Widgets Visibility')
                                    ->description('Toggle on or off to show/hide gadgets on the blog sidebar')
                                    ->schema([
                                        Forms\Components\Toggle::make('showMostReadWidget')
                                            ->label('Show Most Read Articles Widget')
                                            ->default(true),

                                        Forms\Components\Toggle::make('showAuthorWidget')
                                            ->label('Show Author Profile Widget')
                                            ->default(true),

                                        Forms\Components\Toggle::make('showTopicsWidget')
                                            ->label('Show Popular Topics Widget')
                                            ->default(true),
                                    ])->columns(3),
                            ]),

                        // TAB 4: FOOTER
                        Forms\Components\Tabs\Tab::make('🦶 Footer Customization')
                            ->schema([
                                Forms\Components\Toggle::make('showFooter')
                                    ->label('Show Footer Section')
                                    ->default(true),

                                Forms\Components\Section::make('Brand & Tagline Column')
                                    ->schema([
                                        Forms\Components\Textarea::make('footerBio')
                                            ->label('Footer Bio Description')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('footerTagline')
                                            ->label('Footer Motto / Tagline')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Column 2 Links Group')
                                    ->schema([
                                        Forms\Components\TextInput::make('footerCol2Title')
                                            ->label('Column 2 Title')
                                            ->default('Categories')
                                            ->columnSpanFull(),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('footerCol2Link1Text')->label('Link 1 Text'),
                                            Forms\Components\TextInput::make('footerCol2Link1Url')->label('Link 1 URL'),
                                            Forms\Components\TextInput::make('footerCol2Link2Text')->label('Link 2 Text'),
                                            Forms\Components\TextInput::make('footerCol2Link2Url')->label('Link 2 URL'),
                                            Forms\Components\TextInput::make('footerCol2Link3Text')->label('Link 3 Text'),
                                            Forms\Components\TextInput::make('footerCol2Link3Url')->label('Link 3 URL'),
                                            Forms\Components\TextInput::make('footerCol2Link4Text')->label('Link 4 Text'),
                                            Forms\Components\TextInput::make('footerCol2Link4Url')->label('Link 4 URL'),
                                        ]),
                                    ]),

                                Forms\Components\Section::make('Column 3 Links Group')
                                    ->schema([
                                        Forms\Components\TextInput::make('footerCol3Title')
                                            ->label('Column 3 Title')
                                            ->default('Resources')
                                            ->columnSpanFull(),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('footerCol3Link1Text')->label('Link 1 Text'),
                                            Forms\Components\TextInput::make('footerCol3Link1Url')->label('Link 1 URL'),
                                            Forms\Components\TextInput::make('footerCol3Link2Text')->label('Link 2 Text'),
                                            Forms\Components\TextInput::make('footerCol3Link2Url')->label('Link 2 URL'),
                                            Forms\Components\TextInput::make('footerCol3Link3Text')->label('Link 3 Text'),
                                            Forms\Components\TextInput::make('footerCol3Link3Url')->label('Link 3 URL'),
                                            Forms\Components\TextInput::make('footerCol3Link4Text')->label('Link 4 Text'),
                                            Forms\Components\TextInput::make('footerCol3Link4Url')->label('Link 4 URL'),
                                        ]),
                                    ]),

                                Forms\Components\Section::make('Bottom Copyright & Social Links')
                                    ->schema([
                                        Forms\Components\TextInput::make('footerCopyright')
                                            ->label('Footer Copyright Notice')
                                            ->columnSpanFull(),

                                        Forms\Components\Fieldset::make('Social Links (Custom Label & URL)')
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('socialLink1Text')->label('Social 1 Text Label')->default('Twitter / X'),
                                                    Forms\Components\TextInput::make('socialLink1Url')->label('Social 1 Link URL')->default('https://twitter.com'),
                                                    Forms\Components\TextInput::make('socialLink2Text')->label('Social 2 Text Label')->default('GitHub'),
                                                    Forms\Components\TextInput::make('socialLink2Url')->label('Social 2 Link URL')->default('https://github.com'),
                                                    Forms\Components\TextInput::make('socialLink3Text')->label('Social 3 Text Label')->default('LinkedIn'),
                                                    Forms\Components\TextInput::make('socialLink3Url')->label('Social 3 Link URL')->default('https://linkedin.com'),
                                                    Forms\Components\TextInput::make('socialLink4Text')->label('Social 4 Text Label (Optional)'),
                                                    Forms\Components\TextInput::make('socialLink4Url')->label('Social 4 Link URL (Optional)'),
                                                ]),
                                            ]),
                                    ]),
                            ]),

                        // TAB 5: AUTHOR & INSTAGRAM PROFILE
                        Forms\Components\Tabs\Tab::make('📸 Author & Instagram')
                            ->schema([
                                Forms\Components\TextInput::make('authorName')
                                    ->label('Author Name')
                                    ->placeholder('e.g. Alex Rivera or your Instagram Name')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('authorTitle')
                                    ->label('Author Title / Tagline')
                                    ->placeholder('e.g. Editor-in-Chief & Lead Architect')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('authorAvatarUrl')
                                    ->label('Author Profile Picture URL (Instagram Avatar / Image URL)')
                                    ->placeholder('https://images.unsplash.com/photo-...')
                                    ->helperText('Paste your Instagram profile picture URL or any custom avatar image link.')
                                    ->url()
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('authorBio')
                                    ->label('Author Bio Summary')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('authorFollowersCount')
                                    ->label('Followers / Readers Badge Text')
                                    ->placeholder('e.g. 14.2k Followers or 25.8k Readers')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('authorInstagramUrl')
                                    ->label('Instagram Profile Link URL')
                                    ->placeholder('https://instagram.com/your_handle')
                                    ->url()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('authorInstagramHandle')
                                    ->label('Instagram Handle Text')
                                    ->placeholder('e.g. @your_handle')
                                    ->columnSpanFull(),
                            ]),

                        // TAB 6: AD SLOTS & PLACEMENTS
                        Forms\Components\Tabs\Tab::make('📢 Slot Iklan (Ad Placements)')
                            ->schema([
                                Forms\Components\Section::make('Iklan Halaman Home (Beranda)')
                                    ->description('Kelola 4 posisi slot iklan di halaman beranda')
                                    ->schema([
                                        Forms\Components\Toggle::make('showHomeSidebarAd1')
                                            ->label('Aktifkan Iklan Sidebar 1 (Top Sidebar Right)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('homeSidebarAd1Script')
                                            ->label('Kode Skrip HTML / JS Iklan Sidebar 1')
                                            ->placeholder('<script async src="https://pagead2.googlesyndication.com/..."></script>')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showHomeSidebarAd2')
                                            ->label('Aktifkan Iklan Sidebar 2 (Bottom Sidebar Right)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('homeSidebarAd2Script')
                                            ->label('Kode Skrip HTML / JS Iklan Sidebar 2')
                                            ->placeholder('<ins class="adsbygoogle" ...></ins>')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showHomeFeedAd')
                                            ->label('Aktifkan Iklan Feed Utama (Diantara artikel setelah artikel ke-3)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('homeFeedAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Feed Utama')
                                            ->placeholder('<ins class="adsbygoogle" ...></ins>')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showPreFooterAd')
                                            ->label('Aktifkan Iklan Pre-Footer Banner (Sebelum Footer)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('preFooterAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Pre-Footer Banner')
                                            ->placeholder('<ins class="adsbygoogle" ...></ins>')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Iklan Halaman Detail Artikel')
                                    ->description('Kelola 3 posisi slot iklan di halaman detail artikel')
                                    ->schema([
                                        Forms\Components\Toggle::make('showArticleMiddleAd')
                                            ->label('Aktifkan Iklan Tengah Artikel (In-Article Middle)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('articleMiddleAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Tengah Artikel')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showArticleEndAd')
                                            ->label('Aktifkan Iklan Akhir Artikel (In-Article End)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('articleEndAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Akhir Artikel')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showArticleSidebarAd')
                                            ->label('Aktifkan Iklan Kolom Kanan Detail (Article Detail Sidebar)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('articleSidebarAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Sidebar Detail Artikel')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Iklan Halaman Shortener Download (/go/:code)')
                                    ->description('Kelola 5 posisi iklan khusus di halaman artikel perantara shortener (Atas, Tengah, Bawah, Sisi Kanan & Kiri Desktop)')
                                    ->schema([
                                        Forms\Components\Toggle::make('showShortenerTopAd')
                                            ->label('Aktifkan Iklan Atas Shortener (Top Ad)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('shortenerTopAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Atas Shortener')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showShortenerMiddleAd')
                                            ->label('Aktifkan Iklan Tengah Shortener (Middle Ad Dalam Artikel)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('shortenerMiddleAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Tengah Shortener')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showShortenerBottomAd')
                                            ->label('Aktifkan Iklan Bawah Shortener (Bottom Ad Sebelum Komentar)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('shortenerBottomAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Bawah Shortener')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showShortenerLeftAd')
                                            ->label('Aktifkan Iklan Sisi Kiri Desktop (Left Banner / Skyscraper)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('shortenerLeftAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Sisi Kiri Desktop')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('showShortenerRightAd')
                                            ->label('Aktifkan Iklan Sisi Kanan Desktop (Right Banner / Skyscraper)')
                                            ->default(true),
                                        Forms\Components\Textarea::make('shortenerRightAdScript')
                                            ->label('Kode Skrip HTML / JS Iklan Sisi Kanan Desktop')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 7: CUSTOM SCRIPT INJECTION
                        Forms\Components\Tabs\Tab::make('📜 Custom Script Injection')
                            ->schema([
                                Forms\Components\Section::make('📢 Skrip Iklan Global (OTOMATIS NONAKTIF untuk User Premium)')
                                    ->description('Masukkan skrip utama iklan global (seperti Skrip Client Google AdSense, Popunder, Native Ads) yang HARUS OTOMATIS DISISTEMKAN / DIDISABLE bagi pengunjung yang berlangganan Premium.')
                                    ->schema([
                                        Forms\Components\Textarea::make('globalHeadAdScript')
                                            ->label('Skrip Iklan Global Head (Di dalam tag <head>)')
                                            ->placeholder("<!-- Skrip Utama AdSense Client -->\n<script async src=\"https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXX\" crossorigin=\"anonymous\"></script>")
                                            ->helperText('Skrip iklan di sini akan diinjeksi ke <head>, tetapi OTOMATIS DIHAPUS jika pengunjung adalah user Premium.')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('globalBodyOpenAdScript')
                                            ->label('Skrip Iklan Global Body Open (Setelah tag <body> dibuka)')
                                            ->placeholder("<!-- Skrip Iklan Top Body / Banner Injected -->\n<script>...</script>")
                                            ->helperText('Skrip iklan di sini akan diinjeksi setelah <body>, tetapi OTOMATIS DIHAPUS jika pengunjung adalah user Premium.')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('globalBodyCloseAdScript')
                                            ->label('Skrip Iklan Global Body Close (Sebelum tag </body> ditutup)')
                                            ->placeholder("<!-- Skrip Popunder / Auto Ad Network -->\n<script>...</script>")
                                            ->helperText('Skrip iklan di sini akan diinjeksi sebelum </body>, tetapi OTOMATIS DIHAPUS jika pengunjung adalah user Premium.')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('📊 Skrip Analitik & Umum (SELALU AKTIF untuk Semua Visitor)')
                                    ->description('Masukkan skrip umum non-iklan (seperti Google Analytics GA4, Google Tag Manager GTM, Meta Pixel, Hotjar, dll.) yang TIDAK PERLU DIHAPUS untuk pengunjung Premium.')
                                    ->schema([
                                        Forms\Components\Textarea::make('headScriptContent')
                                            ->label('Head Script Injection (Didalam tag <head>)')
                                            ->placeholder("<!-- Google Analytics GA4 -->\n<script async src=\"https://www.googletagmanager.com/gtag/js?id=G-XXXXX\"></script>")
                                            ->helperText('Skrip analitik/umum di sini akan SELALU AKTIF untuk seluruh pengunjung (termasuk user Premium).')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('bodyOpenScriptContent')
                                            ->label('Body Open Script Injection (Setelah tag <body> dibuka)')
                                            ->placeholder("<!-- Google Tag Manager (noscript) -->\n<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=GTM-XXXX\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>")
                                            ->helperText('Skrip analitik/umum di sini akan SELALU AKTIF untuk seluruh pengunjung.')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('bodyCloseScriptContent')
                                            ->label('Body Close Script Injection (Sebelum tag </body> ditutup)')
                                            ->placeholder("<!-- Live Chat / General Tracking -->\n<script>console.log('App ready');</script>")
                                            ->helperText('Skrip analitik/umum di sini akan SELALU AKTIF untuk seluruh pengunjung.')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // TAB 8: PREMIUM SUBSCRIPTION SETTINGS
                        Forms\Components\Tabs\Tab::make('🌟 Berlangganan Premium')
                            ->schema([
                                Forms\Components\Section::make('Konfigurasi Pembelian Token Premium')
                                    ->description('Atur link WhatsApp & harga berlangganan premium')
                                    ->schema([
                                        Forms\Components\TextInput::make('premiumBuyUrl')
                                            ->label('Link WhatsApp Pembelian Token Premium')
                                            ->placeholder('https://wa.me/6285262335849?text=Min%20Saya%20mau%20beli%20token%20Script%20MLBB')
                                            ->helperText('Pengguna akan diarahkan ke URL WhatsApp ini saat mengklik "Beli Token Premium"')
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('premiumMonthlyPrice')
                                            ->label('Label Harga Berlangganan (per bulan)')
                                            ->placeholder('5.000')
                                            ->default('5.000')
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('premiumFreeAdUrl')
                                            ->label('Link Iklan Smartlink / Sponsor (Klaim Premium 1 Hari Gratis)')
                                            ->placeholder('https://scriptmlbb.com atau URL Smartlink Iklan')
                                            ->default('https://scriptmlbb.com')
                                            ->helperText('URL Smartlink iklan sponsor yang akan dibuka di tab baru saat pengguna mengklik tombol "Klaim Premium 1 Hari Gratis (15s)"')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $group = 'general';
            if (str_contains($key, 'Widget')) $group = 'widgets';
            if (str_contains($key, 'footer') || str_contains($key, 'Url') || str_contains($key, 'social')) $group = 'footer';
            if (str_contains($key, 'brand') || str_contains($key, 'announcement')) $group = 'header';
            if (str_contains($key, 'meta') || str_contains($key, 'favicon') || str_contains($key, 'robots') || str_contains($key, 'ads')) $group = 'seo';

            SiteSetting::set($key, $value, $group);
        }

        $this->js("new FilamentNotification().title('Settings Saved Successfully').body('Blogger site settings updated! All frontend visitors will see the updated layout.').success().send()");
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save & Publish Settings')
                ->icon('heroicon-m-check')
                ->color('primary')
                ->size('lg')
                ->action('save'),
        ];
    }
}
