<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Script MLBB Admin')
            ->favicon(asset('favicon.svg'))
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString('
                    <style>
                        /* Sticky Toolbar & Scrollable Editor Container */
                        .fi-fo-rich-editor {
                            position: relative !important;
                        }
                        .fi-fo-rich-editor-toolbar {
                            position: sticky !important;
                            top: 0 !important;
                            z-index: 20 !important;
                            background-color: #ffffff !important;
                            border-bottom: 1px solid #e5e7eb !important;
                        }
                        .dark .fi-fo-rich-editor-toolbar {
                            background-color: #18181b !important;
                            border-bottom-color: #27272a !important;
                        }
                        .fi-fo-rich-editor .ProseMirror,
                        .fi-fo-rich-editor-main {
                            max-height: 500px !important;
                            overflow-y: auto !important;
                            min-height: 250px !important;
                        }
                    </style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\BlogStatsOverview::class,
                \App\Filament\Widgets\LatestArticlesWidget::class,
                \App\Filament\Widgets\PendingCommentsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
