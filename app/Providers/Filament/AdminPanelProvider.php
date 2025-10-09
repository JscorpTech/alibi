<?php

namespace App\Providers\Filament;

use App\Filament\Resources\LoginResource\Pages\Login;
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


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->renderHook('panels::head.end', fn() => view('filament.hooks.fileupload-plus-styles'))
            ->renderHook('panels::body.end', fn() => view('filament.hooks.fileupload-plus-script'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Purple,
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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

// <?php

// namespace App\Providers\Filament;

// use Filament\Panel;
// use Filament\PanelProvider;
// use Filament\Support\Colors\Color;
// use Filament\Http\Middleware\Authenticate;
// use Filament\Http\Middleware\DisableBladeIconComponents;
// use Filament\Http\Middleware\DispatchServingFilamentEvent;
// use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
// use Illuminate\Cookie\Middleware\EncryptCookies;
// use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
// use Illuminate\Routing\Middleware\SubstituteBindings;
// use Illuminate\Session\Middleware\AuthenticateSession;
// use Illuminate\Session\Middleware\StartSession;
// use Illuminate\View\Middleware\ShareErrorsFromSession;
// use Filament\Pages;
// use Filament\Widgets;
// use Filament\Support\Enums\ThemeMode;

// class AdminPanelProvider extends PanelProvider
// {
//     public function panel(Panel $panel): Panel
//     {
//         return $panel

//             ->default(true)
//             ->id('admin')
//             ->path('admin')

//             ->login()
//             ->authMiddleware([Authenticate::class])

//             ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
//             ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
//             ->pages([Pages\Dashboard::class])
//             ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
//             ->widgets([
//                 Widgets\AccountWidget::class,
//                 Widgets\FilamentInfoWidget::class,
//             ])

//             // Брендинг/цвета/шрифт
//             ->brandName('ALB. Admin')
//             ->brandLogo(asset('images/alb-logo.svg'))
//             ->brandLogoHeight('2rem')
//             ->favicon(asset('images/favicon.png'))
//             ->colors([
//                 'primary' => Color::hex('#FF007F'),
//                 'success' => Color::hex('#00E676'),
//                 'danger'  => Color::hex('#FF1744'),
//                 'gray'    => Color::hex('#F6F6F6'),
//             ])
//             ->font('Poppins')
//             ->darkMode(true)
//             ->sidebarCollapsibleOnDesktop()
//             ->sidebarWidth('18rem')

//             // Подключаем свою тему (Tailwind + кастомные стили)
//             ->viteTheme('resources/css/filament/admin/theme.css')

//            ->renderHook('panels::topbar.end', fn () => view('filament.hooks.top-tabs'))

//             // Вставляем свои куски разметки через render hooks
//             ->renderHook('panels::body.start', fn () => view('filament.hooks.body-start'))
//             ->renderHook('panels::body.end', fn () => view('filament.hooks.body-end'))

//             ->middleware([
//                 EncryptCookies::class,
//                 AddQueuedCookiesToResponse::class,
//                 StartSession::class,
//                 AuthenticateSession::class,
//                 ShareErrorsFromSession::class,
//                 VerifyCsrfToken::class,
//                 SubstituteBindings::class,
//                 DisableBladeIconComponents::class,
//                 DispatchServingFilamentEvent::class,
//             ]);
//     }
// }
