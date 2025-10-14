<x-filament-panels::page>
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .animate-scale-in { animation: scaleIn 0.3s ease-out; }
        .shadow-premium { box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04); }
        .hover-lift { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 12px 48px rgba(0, 0, 0, 0.12); }
        .glass-effect { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>

    <div class="space-y-6">

        {{-- 📊 HEADER + ФИЛЬТРЫ --}}
        <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 shadow-premium animate-slide-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="text-white">
                    <h1 class="text-3xl font-bold flex items-center gap-3">📊 Аналитика и отчёты</h1>
                    <p class="text-slate-300 mt-2 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Период: <span class="font-semibold">{{ $from }} — {{ $to }}</span></span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button wire:click="setToday"
                        class="inline-flex items-center gap-2 px-4 py-2.5 glass-effect rounded-xl text-sm font-semibold text-slate-900 hover:bg-white transition-all shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Сегодня
                    </button>

                    <div class="flex items-center gap-2 glass-effect rounded-xl px-4 py-2 shadow-md">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-slate-700">С</span>
                        <input type="date" wire:model.live="from"
                            class="border-0 rounded-lg text-sm font-medium bg-transparent focus:ring-2 focus:ring-emerald-500 py-1"/>
                        <span class="text-sm font-medium text-slate-700">по</span>
                        <input type="date" wire:model.live="to"
                            class="border-0 rounded-lg text-sm font-medium bg-transparent focus:ring-2 focus:ring-emerald-500 py-1"/>
                        <button wire:click="refreshReport"
                            class="ml-2 px-4 py-1.5 rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-sm font-semibold hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-md">
                            Показать
                        </button>
                    </div>

                    <button class="inline-flex items-center gap-2 px-4 py-2.5 glass-effect rounded-xl text-sm font-semibold text-slate-900 hover:bg-white transition-all shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Экспорт
                    </button>
                </div>
            </div>
        </div>

        {{-- 💎 KPI КАРТОЧКИ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 animate-fade-in">
            @php
                $cards = [
                    ['label' => 'Выручка', 'value' => $kpi['revenue'] ?? 0, 'icon' => '💰', 'gradient' => 'from-blue-500 to-blue-600', 'text' => 'text-blue-600'],
                    ['label' => 'Товаров продано', 'value' => $kpi['items_sold'] ?? 0, 'icon' => '📦', 'gradient' => 'from-purple-500 to-purple-600', 'text' => 'text-purple-600'],
                    ['label' => 'Возвраты', 'value' => $kpi['returns_amount'] ?? 0, 'icon' => '🔄', 'gradient' => 'from-orange-500 to-orange-600', 'text' => 'text-orange-600'],
                    ['label' => 'Чеков', 'value' => $kpi['orders_count'] ?? 0, 'icon' => '🧾', 'gradient' => 'from-green-500 to-green-600', 'text' => 'text-green-600'],
                    ['label' => 'Средний чек', 'value' => $kpi['avg_receipt'] ?? 0, 'icon' => '💳', 'gradient' => 'from-pink-500 to-pink-600', 'text' => 'text-pink-600'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="group rounded-2xl bg-white border-2 border-slate-100 p-5 hover-lift shadow-md transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $card['gradient'] }} flex items-center justify-center text-2xl shadow-lg">
                            {{ $card['icon'] }}
                        </div>
                    </div>
                    <div class="text-sm font-medium text-slate-600 mb-1">{{ $card['label'] }}</div>
                    <div class="text-3xl font-bold {{ $card['text'] }}">
                        {{ number_format((int) $card['value'], 0, '.', ' ') }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🔥 ТОП ПРОДАЖИ --}}
        @php
            $items = collect($top ?? [])->map(function ($row, $i) {
                $toUrl = function (?string $p): ?string {
                    if (!$p) return null;
                    if (str_starts_with($p, 'http://') || str_starts_with($p, 'https://') || str_starts_with($p, '/storage/')) {
                        return $p;
                    }
                    return \Illuminate\Support\Facades\Storage::url($p);
                };
                return [
                    'rank' => $i + 1,
                    'id' => $row['product_id'] ?? null,
                    'name' => $row['name'] ?? ($row['product_name'] ?? ('Товар #' . ($row['product_id'] ?? ''))),
                    'qty' => (int) ($row['qty'] ?? $row['count'] ?? 0),
                    'revenue' => (int) ($row['revenue'] ?? $row['amount'] ?? $row['total'] ?? 0),
                    'image' => $toUrl($row['image'] ?? null),
                ];
            });
            $maxQty = max(1, (int) $items->max('qty'));
        @endphp

        <div class="rounded-2xl bg-white border-2 border-slate-100 shadow-premium overflow-hidden animate-scale-in">
            <div class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b-2 border-slate-100">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">🏆 Топ продаж</h2>
                    <p class="text-sm text-slate-600 mt-1">Лидеры по количеству за период</p>
                </div>
                <button type="button" wire:click="setTab('top')"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg">
                    Открыть детали →
                </button>
            </div>

            @if($items->isNotEmpty())
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
                        @foreach ($items as $it)
                            @php
                                $badge = match ($it['rank']) {
                                    1 => ['text' => '🥇 ТОП-1', 'class' => 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white shadow-lg'],
                                    2 => ['text' => '🥈 ТОП-2', 'class' => 'bg-gradient-to-r from-slate-300 to-slate-400 text-white shadow-lg'],
                                    3 => ['text' => '🥉 ТОП-3', 'class' => 'bg-gradient-to-r from-amber-600 to-amber-700 text-white shadow-lg'],
                                    default => ['text' => 'ТОП-' . $it['rank'], 'class' => 'bg-slate-100 text-slate-700 border border-slate-200'],
                                };
                                $progress = intval(($it['qty'] / $maxQty) * 100);
                            @endphp

                            <div class="group relative rounded-2xl border-2 border-slate-100 bg-white p-4 hover-lift hover:border-emerald-400 transition-all">
                                <div class="absolute top-3 left-3 z-10 px-3 py-1 rounded-xl text-xs font-bold {{ $badge['class'] }}">
                                    {{ $badge['text'] }}
                                </div>

                                <div class="w-full aspect-[4/5] rounded-xl overflow-hidden bg-gradient-to-br from-slate-50 to-slate-100 shadow-md">
                                    @if(!empty($it['image']))
                                        <img src="{{ $it['image'] }}" alt="" class="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-110">
                                    @else
                                        <div class="grid h-full w-full place-items-center">
                                            <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    <div class="text-sm font-semibold text-slate-900 truncate" title="{{ $it['name'] }}">{{ $it['name'] }}</div>
                                </div>

                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">Продано</span>
                                        <span class="font-bold text-emerald-600">{{ number_format($it['qty'], 0, '.', ' ') }} шт</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600">Выручка</span>
                                        <span class="font-bold text-slate-900">{{ number_format($it['revenue'], 0, '.', ' ') }} сум</span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                                        <span>Относительно лидера</span>
                                        <span class="font-bold">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 transition-all duration-500" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="px-6 py-16 text-center">
                    <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-slate-500 font-medium">Нет данных по топ-продажам</p>
                    <p class="text-sm text-slate-400 mt-1">Попробуйте выбрать другой период</p>
                </div>
            @endif
        </div>
        {{-- 📋 ВСЕ ПРОДАЖИ --}}
        <div class="rounded-2xl bg-white border-2 border-slate-100 shadow-premium overflow-hidden">
            <div class="border-b-2 border-slate-100 px-6 pt-5 bg-gradient-to-r from-slate-50 to-white">
                <div class="flex items-center gap-2">
                    @php $tabs = ['allsales' => ['label' => 'Все продажи', 'icon' => '📋']]; @endphp
                    @foreach($tabs as $key => $info)
                        <button type="button" wire:click="setTab('{{ $key }}')"
                            class="relative px-6 py-3 text-sm font-semibold rounded-t-xl transition-all {{ $tab === $key ? 'text-emerald-600 bg-white -mb-[2px]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <span>{{ $info['icon'] }}</span>
                                <span>{{ $info['label'] }}</span>
                            </span>
                            @if($tab === $key)
                                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="p-5 bg-slate-50 border-b-2 border-slate-100">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[300px] relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Поиск по номеру чека, клиенту, кассиру..."
                            class="w-full pl-12 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"/>
                    </div>
                    <button class="inline-flex items-center gap-2 px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Сортировка
                    </button>
                    <button class="inline-flex items-center gap-2 px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Фильтры
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($tab === 'allsales')
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b-2 border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Чек #</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Дата и время</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Кассир</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Клиент</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Тип</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Источник</th>
                                <th class="px-6 py-4 text-center font-semibold text-slate-700">Кол-во</th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-700">Сумма</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($allSales as $row)
                                <tr class="group hover:bg-emerald-50 cursor-pointer transition-colors" wire:click="openGroup({{ $row['group_id'] ?? $row['id'] }})">
                                    <td class="px-6 py-4">
                                        <span class="font-mono font-semibold text-slate-900">#{{ $row['order_number'] ?? $row['id'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $row['paid_at'] }}</td>
                                    <td class="px-6 py-4 text-slate-900 font-medium">{{ $row['cashier_name'] }}</td>
                                    <td class="px-6 py-4 text-slate-900 font-medium">{{ $row['client_name'] }}</td>
                                    @php
                                        $typeMap = ['sale' => 'Продажа', 'exchange' => 'Обмен', 'return' => 'Возврат'];
                                        $typeIcons = ['sale' => '✅', 'exchange' => '🔄', 'return' => '↩️'];
                                        $color = [
                                            'sale' => 'bg-green-100 text-green-700 border-green-200',
                                            'exchange' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'return' => 'bg-red-100 text-red-700 border-red-200',
                                        ][$row['type'] ?? ''] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                        $typeRu = $typeMap[$row['type'] ?? ''] ?? ucfirst((string) ($row['type'] ?? '—'));
                                        $icon = $typeIcons[$row['type'] ?? ''] ?? '📄';
                                    @endphp
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border {{ $color }}">
                                            <span>{{ $icon }}</span>
                                            <span>{{ $typeRu }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-mono font-semibold uppercase">{{ $row['source'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-900 font-bold text-sm">{{ $row['items_count'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-base text-slate-900">{{ number_format($row['total'], 0, '.', ' ') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-slate-500 font-medium">Нет продаж за выбранный период</p>
                                        <p class="text-sm text-slate-400 mt-1">Попробуйте изменить фильтры</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>

    {{-- 🔍 МОДАЛЬНОЕ ОКНО --}}
    @if($showGroupModal)
        <div class="fixed inset-0 z-[999] flex items-center justify-center p-4 animate-fade-in">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeGroup"></div>

            <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-2xl overflow-hidden animate-scale-in">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-slate-800">
                    <div class="flex items-start justify-between">
                        <div class="text-white">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-2xl font-bold">Чек #{{ $groupCard['order_number'] ?? '—' }}</h3>
                                @php
                                    $type = $groupCard['type'] ?? '';
                                    $map = ['sale' => 'Продажа', 'exchange' => 'Обмен', 'return' => 'Возврат'];
                                    $icons = ['sale' => '✅', 'exchange' => '🔄', 'return' => '↩️'];
                                    $cls = [
                                        'sale' => 'bg-green-500 text-white',
                                        'exchange' => 'bg-amber-500 text-white',
                                        'return' => 'bg-red-500 text-white',
                                    ][$type] ?? 'bg-slate-500 text-white';
                                @endphp
                                <span class="px-3 py-1 rounded-lg text-sm font-bold {{ $cls }} flex items-center gap-1.5">
                                    <span>{{ $icons[$type] ?? '📄' }}</span>
                                    <span>{{ $map[$type] ?? '—' }}</span>
                                </span>
                            </div>
                            <div class="text-sm text-slate-300 space-x-4">
                                <span>📅 {{ $groupCard['paid_at'] ?? '—' }}</span>
                                <span>👤 Кассир: {{ $groupCard['cashier'] ?? '—' }}</span>
                                <span>🛒 Клиент: {{ $groupCard['client'] ?? '—' }}</span>
                                <span>📍 {{ strtoupper($groupCard['source'] ?? '—') }}</span>
                            </div>
                        </div>
                        <button class="text-white/80 hover:text-white text-2xl leading-none" wire:click="closeGroup">✕</button>
                    </div>
                </div>

                {{-- Таблица позиций --}}
                <div class="max-h-[60vh] overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b-2 border-slate-100 sticky top-0">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Товар</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700">Характеристики</th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-700">Цена</th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-700">Скидка</th>
                                <th class="px-6 py-4 text-center font-semibold text-slate-700">Кол-во</th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-700">Итого</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($groupItems as $it)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-14 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                                                @php
                                                    $img = $it['image'] ?? null;
                                                    if ($img && !str_starts_with($img, 'http') && !str_starts_with($img, '/storage/')) {
                                                        $img = \Illuminate\Support\Facades\Storage::url($img);
                                                    }
                                                @endphp
                                                @if($img)
                                                    <img src="{{ $img }}" class="w-full h-full object-cover" alt="">
                                                @else
                                                    <div class="w-full h-full grid place-items-center">
                                                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-900">{{ $it['name'] }}</div>
                                                @if(!empty($it['sku']))
                                                    <div class="text-xs text-slate-500 font-mono mt-0.5">SKU: {{ $it['sku'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if(!empty($it['size']))
                                                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-xs font-medium">
                                                    📏 {{ $it['size'] }}
                                                </span>
                                            @endif
                                            @if(!empty($it['color']))
                                                <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 text-xs font-medium">
                                                    🎨 {{ $it['color'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-900">
                                        {{ number_format($it['price'], 0, '.', ' ') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-600">
                                        {{ number_format($it['discount'], 0, '.', ' ') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center">
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 text-slate-900 font-bold">
                                                {{ $it['qty'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-base text-emerald-600">
                                            {{ number_format($it['line_total'], 0, '.', ' ') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer с итогом --}}
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-t-2 border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-slate-600">
                            <span class="font-medium">ID чека:</span>
                            <span class="font-mono font-semibold text-slate-900 ml-2">{{ $groupCard['id'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm font-medium text-slate-700">Итого по чеку:</span>
                            <span class="text-2xl font-bold text-emerald-600">
                                {{ number_format((int) ($groupCard['total'] ?? 0), 0, '.', ' ') }} сум
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>