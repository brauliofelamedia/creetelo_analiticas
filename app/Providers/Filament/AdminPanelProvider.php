<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Visualbuilder\EmailTemplates\EmailTemplatesPlugin;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TransactionsPerMonth;
use App\Filament\Widgets\TransactionsPerDay;
use Filament\Navigation\NavigationItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(asset('images/logo.webp'))
            ->brandLogoHeight('5rem')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationGroups([
                'Shop',
                'Blog',
                'Transacciones',
            ])
            ->navigationItems([
                NavigationItem::make('Comparación por días')
                    ->icon('heroicon-o-funnel')
                    ->url('/admin/transactions/day')
                    ->isActiveWhen(fn (): bool => request()->url() === url('/admin/transactions/day'))
                    ->group('Estadísticas'),
                NavigationItem::make('Comparación de meses')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->url('/admin/transactions/month')
                    ->isActiveWhen(fn (): bool => request()->url() === url('/admin/transactions/month'))
                    ->group('Estadísticas'),
                NavigationItem::make('Proyecciones')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->url('/admin/subscriptions/projection')
                    ->isActiveWhen(fn (): bool => request()->url() === url('/admin/subscriptions/projection'))
                    ->group('Estadísticas')
            ])
            ->resources([
                //config('filament-logger.activity_resource')
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            //->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                StatsOverview::class,
                TransactionsPerMonth::class,
                TransactionsPerDay::class,
                //MonthlyEarningsOverview::class,
                //ContactsPerMonthChart::class,
                //ContactsAnualPerMonthChart::class,
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
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                \Hasnayeen\Themes\ThemesPlugin::make(),
                BreezyCore::make()
                    ->enableTwoFactorAuthentication(
                        force: false,
                    )
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        userMenuLabel: 'Perfil',
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Configuración',
                        hasAvatars: false,
                        slug: 'perfil'
                    )
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
