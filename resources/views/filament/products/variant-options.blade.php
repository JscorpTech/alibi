@php
    /** @var \Closure $get */
    // Собираем оси и значения
    $options = collect($get('variant_options') ?? [])
        ->map(fn ($o) => [
            'name'   => trim((string) data_get($o, 'name')),
            'values' => array_values(array_filter((array) data_get($o, 'values'))),
        ])
        ->filter(fn ($o) => $o['name'] && $o['values'])
        ->values();

    // Подсчёт количества комбинаций
    $combinationCount = $options->isEmpty()
        ? 0
        : $options->pluck('values')->map(fn ($v) => count($v))->reduce(fn ($c, $n) => $c * $n, 1);
@endphp

<div
    x-data="{
        openHelp: false,
        scrollToRepeater() {
            const el = document.querySelector('[data-field=\"variant_options\"]') || document.querySelector('[wire\\:id]');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        },
        copy(text) {
            navigator.clipboard?.writeText(text)
        },
    }"
    class="fi-section max-w-full"
>
    {{-- Карточка-контейнер (мягкий градиент, тонкое свечение, поддержка dark) --}}
    <div
        class="rounded-2xl border border-gray-200/70 bg-gradient-to-b from-white to-gray-50/60 p-6 md:p-7 shadow-sm ring-1 ring-black/5
               dark:from-gray-900/60 dark:to-gray-900/20 dark:border-white/10 dark:ring-white/5"
    >
        {{-- Заголовок --}}
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-gray-900 text-white text-[10px]
                                 dark:bg-white dark:text-gray-900">VAR</span>
                    <h3 class="text-lg font-semibold tracking-tight">Варианты товара</h3>
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Добавьте оси (например, «Размер», «Цвет») и значения — затем автоматически сформируйте все комбинации.
                </p>
            </div>

            {{-- Быстрая помощь (кнопка-переключатель) --}}
            <button
                type="button"
                @click="openHelp = !openHelp"
                @keydown.escape.window="openHelp = false"
                :aria-expanded="openHelp"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium
                       hover:bg-gray-50 transition dark:border-white/10 dark:hover:bg-white/5"
            >
                <x-heroicon-o-question-mark-circle class="h-4 w-4"/>
                Подсказка
            </button>
        </div>

        {{-- Всплывающая помощь (плавное появление) --}}
        <div
            x-show="openHelp"
            x-transition.opacity.duration.200ms
            class="mt-4 rounded-xl border border-dashed border-gray-200/80 bg-white/70 backdrop-blur p-4 text-sm leading-relaxed
                   dark:bg-white/5 dark:border-white/10"
        >
            <ol class="list-decimal pl-5 space-y-1">
                <li>В репитере добавьте <b>оси</b> и их <b>значения</b>.</li>
                <li>Нажмите <b>Готово</b> у списка — сформируем все комбинации.</li>
                <li>Отредактируйте цену, остаток и SKU в редакторе вариантов.</li>
            </ol>
            <div class="mt-3">
                <button
                    type="button"
                    @click="scrollToRepeater()"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-950 px-3 py-1.5 text-xs font-medium text-white hover:opacity-90
                           dark:bg-white dark:text-gray-900"
                >
                    <x-heroicon-o-arrow-down-circle class="h-4 w-4"/>
                    К списку параметров
                </button>
            </div>
        </div>

        {{-- Содержимое --}}
        @if ($options->isEmpty())
            {{-- Пустое состояние — более «живое» --}}
            <div class="mt-6 grid gap-6 md:grid-cols-2 md:items-center">
                <div class="order-2 md:order-1">
                    <h4 class="font-medium">Начните с добавления осей</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-plus-circle class="mt-0.5 h-4 w-4"/>
                            Нажмите <b>«Добавить ось»</b> и выберите «Размер» или «Цвет».
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="mt-0.5 h-4 w-4"/>
                            Укажите несколько значений — например, <i>S, M, L</i> или <i>Красный, Синий</i>.
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-sparkles class="mt-0.5 h-4 w-4"/>
                            Нажмите <b>«Готово»</b> — варианты создадутся автоматически.
                        </li>
                    </ul>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <button
                            type="button"
                            @click="scrollToRepeater()"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-950 px-4 py-2 text-white text-sm font-medium
                                   hover:opacity-90 transition dark:bg-white dark:text-gray-900"
                        >
                            <x-heroicon-o-plus class="h-4 w-4"/>
                            Добавить ось
                        </button>

                        <button
                            type="button"
                            @click="openHelp = true; scrollToRepeater()"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium
                                   hover:bg-gray-50 transition dark:border-white/10 dark:hover:bg-white/5"
                        >
                            <x-heroicon-o-light-bulb class="h-4 w-4"/>
                            Как это работает?
                        </button>
                    </div>
                </div>

                {{-- Мини-иллюстрация --}}
                <div class="order-1 md:order-2">
                    <div class="mx-auto aspect-[4/3] w-full max-w-md rounded-xl border border-dashed border-gray-200/80 p-6
                                bg-white/60 backdrop-blur dark:bg-white/5 dark:border-white/10">
                        <div class="h-full w-full rounded-lg bg-gradient-to-br from-gray-100 to-white dark:from-white/5 dark:to-transparent
                                    flex items-center justify-center">
                            <svg viewBox="0 0 200 120" class="h-28 w-44 opacity-70">
                                <rect x="10" y="15" width="180" height="18" rx="4" class="fill-gray-300/50 dark:fill-white/20"/>
                                <rect x="10" y="45" width="55" height="18" rx="4" class="fill-gray-300/50 dark:fill-white/20"/>
                                <rect x="70" y="45" width="55" height="18" rx="4" class="fill-gray-300/50 dark:fill-white/20"/>
                                <rect x="130" y="45" width="55" height="18" rx="4" class="fill-gray-300/50 dark:fill-white/20"/>
                                <rect x="10" y="80" width="170" height="8" rx="4" class="fill-gray-300/40 dark:fill-white/10"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Когда оси уже заданы --}}
            <div class="mt-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <span class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white/70 px-2.5 py-1
                                     dark:border-white/10 dark:bg-white/5">
                            <x-heroicon-o-variable class="h-4 w-4"/>
                            Оси: <b class="ml-1">{{ $options->count() }}</b>
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white/70 px-2.5 py-1
                                     dark:border-white/10 dark:bg-white/5">
                            <x-heroicon-o-squares-2x2 class="h-4 w-4"/>
                            Комбинаций: <b class="ml-1">{{ $combinationCount }}</b>
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="scrollToRepeater()"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium
                                   hover:bg-gray-50 transition dark:border-white/10 dark:hover:bg-white/5"
                        >
                            <x-heroicon-o-plus class="h-4 w-4"/>
                            Добавить ещё ось
                        </button>

                        <button
                            type="button"
                            @click="scrollToRepeater(); openHelp = true"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-950 px-3 py-1.5 text-sm font-medium text-white
                                   hover:opacity-90 transition dark:bg-white dark:text-gray-900"
                        >
                            <x-heroicon-o-check class="h-4 w-4"/>
                            Сформировать комбинации
                        </button>
                    </div>
                </div>

                {{-- Список осей с чипсами значений --}}
                <div class="grid gap-4">
                    @foreach ($options as $opt)
                        <div class="rounded-xl border border-gray-200/70 bg-white/60 backdrop-blur p-4
                                    hover:border-gray-300 transition dark:bg-white/5 dark:border-white/10 dark:hover:border-white/20">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold tracking-tight">{{ $opt['name'] }}</div>
                                    @if (count($opt['values']))
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach ($opt['values'] as $v)
                                                <span
                                                    x-data="{copied:false, t:null}"
                                                    @mouseleave="copied=false; if(t){clearTimeout(t)}"
                                                    class="group inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white/80 px-2 py-1
                                                           text-xs font-medium text-gray-700 hover:bg-gray-50 transition
                                                           dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10"
                                                >
                                                    {{ $v }}
                                                    <button
                                                        type="button"
                                                        class="opacity-60 group-hover:opacity-100 transition"
                                                        :aria-label="copied ? 'Скопировано' : 'Скопировать'"
                                                        title="Скопировать"
                                                        @click.stop="
                                                            $root.copy('{{ addslashes($v) }}');
                                                            copied = true;
                                                            if(t){clearTimeout(t)}
                                                            t = setTimeout(() => copied = false, 1200);
                                                        "
                                                    >
                                                        <template x-if="!copied">
                                                            <x-heroicon-o-clipboard class="h-3.5 w-3.5"/>
                                                        </template>
                                                        <template x-if="copied">
                                                            <x-heroicon-o-check class="h-3.5 w-3.5"/>
                                                        </template>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Бейдж количества --}}
                                <span class="shrink-0 rounded-lg bg-gray-100/80 px-2 py-1 text-xs font-medium text-gray-700
                                             dark:bg-white/10 dark:text-gray-200">
                                    {{ count($opt['values']) }} знач.
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Подсказка внизу --}}
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Нажмите <b>«Готово»</b> у списка параметров, чтобы создать {{ $combinationCount }} комбинац{{ $combinationCount == 1 ? 'ию' : 'ий' }}.
                </div>
            </div>
        @endif
    </div>
</div>