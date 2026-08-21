<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('SIMASJID')
            ->brandLogo(fn (): View => view('filament.brand'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->darkMode(false)
            ->sidebarWidth('18rem')
            ->maxContentWidth('7xl')
            ->viteTheme('resources/css/filament/admin/theme.css')
            // Satu grup = satu modul pada sidebar (meniru menu mockup).
            ->navigationGroups([
                NavigationGroup::make('Profil & Struktur')
                    ->icon('heroicon-o-users')
                    ->collapsed(),
                NavigationGroup::make('Program & Kegiatan')
                    ->icon('heroicon-o-calendar-days')
                    ->collapsed(),
                NavigationGroup::make('Jadwal Ibadah')
                    ->icon('heroicon-o-clock')
                    ->collapsed(),
                NavigationGroup::make('Keuangan')
                    ->icon('heroicon-o-wallet')
                    ->collapsed(),
                NavigationGroup::make('Pengumuman')
                    ->icon('heroicon-o-megaphone')
                    ->collapsed(),
                NavigationGroup::make('Ekstensi')
                    ->icon('heroicon-o-squares-plus')
                    ->collapsed(),
                NavigationGroup::make('Pengaturan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): View => view('filament.hooks.entitas-switcher'),
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): View => view('filament.hooks.topbar-tanggal'),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): View => view('filament.hooks.sidebar-user'),
            )
            ->renderHook(
                PanelsRenderHook::SIMPLE_PAGE_START,
                fn (): View => view('filament.hooks.login-kembali'),
            )
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
