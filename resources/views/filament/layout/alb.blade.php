{{-- Минималистичный фирменный layout ALB. --}}
{{-- Можно смело править фон/градиенты/blur/отступы --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    {{ \Filament\Panel::getCurrent()?->getHead() }}
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0B0B0B] via-[#121212] to-[#FF007F22] text-white antialiased">
    <div class="flex min-h-screen">
        {{-- Сайдбар Filament (можно стилизовать классами Tailwind) --}}
        <x-filament::sidebar
            class="bg-black/60 backdrop-blur-xl border-none text-white"
        />

        {{-- Основная область --}}
        <main class="flex-1 p-6 lg:p-8">
            {{ \Filament\Panel::renderHook('panels::body.start') }}

            {{-- Контент панели --}}
            {{ $slot }}

            {{ \Filament\Panel::renderHook('panels::body.end') }}
        </main>
    </div>

    {{ \Filament\Panel::getCurrent()?->getScripts() }}
</body>
</html>