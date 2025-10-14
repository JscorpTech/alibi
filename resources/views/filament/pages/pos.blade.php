<x-filament-panels::page>
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .shadow-premium {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .hover-lift {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.12);
        }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ========== ЛЕВАЯ КОЛОНКА: КАТАЛОГ ========== --}}
        <div class="lg:col-span-8 space-y-5">

            {{-- 🔍 ПРЕМИУМ ПОИСК --}}
            <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 shadow-premium">
                <div class="flex items-center gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                            wire:model.debounce.300ms="barcode" 
                            wire:keydown.enter.prevent="scan" 
                            autofocus
                            class="block w-full pl-12 pr-4 py-4 rounded-xl bg-white/95 border-0 text-slate-900 placeholder-slate-400 shadow-lg focus:ring-2 focus:ring-emerald-500 transition-all text-base font-medium"
                            placeholder="🔎 Сканируйте штрихкод или введите название товара..." />
                    </div>
                    <button type="button" 
                        wire:click="scan"
                        class="group relative px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold shadow-xl hover:shadow-2xl hover:from-emerald-600 hover:to-emerald-700 transition-all hover-lift">
                        <span class="relative z-10">Добавить</span>
                    </button>
                </div>
                <div class="mt-3 flex items-center gap-2 text-slate-300 text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Быстрый поиск: barcode / SKU / название • Enter для добавления</span>
                </div>
            </div>

            {{-- 📦 РЕЗУЛЬТАТЫ ПОИСКА --}}
            @if (!empty($this->results))
                <div class="rounded-2xl bg-white shadow-premium p-6 animate-slide-in">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-slate-900">Найденные товары</h3>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm font-semibold">
                            {{ count($this->results) }} товаров
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($this->results as $p)
                            <div class="group relative rounded-2xl border-2 border-slate-100 bg-white p-4 hover-lift hover:border-emerald-500 transition-all duration-300">
                                
                                {{-- 📸 ФОТО --}}
                                <div class="relative w-full aspect-[4/5] rounded-xl overflow-hidden bg-gradient-to-br from-slate-50 to-slate-100 mb-3">
                                    @if (!empty($p['image']))
                                        <img src="{{ $p['image'] }}" 
                                            alt="{{ $p['name'] }}"
                                            class="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-110"
                                            loading="lazy">
                                    @else
                                        <div class="grid h-full w-full place-items-center">
                                            <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    {{-- Бейдж остатка --}}
                                    @if(!empty($p['qty_total']))
                                        <div class="absolute top-2 right-2 px-2.5 py-1 rounded-lg glass-effect text-xs font-bold text-slate-700">
                                            📦 {{ $p['qty_total'] }} шт
                                        </div>
                                    @endif
                                </div>

                                {{-- 🏷 НАЗВАНИЕ + ЦЕНА --}}
                                <div class="mb-3">
                                    <h4 class="font-semibold text-slate-900 text-sm leading-tight truncate mb-1" title="{{ $p['name'] }}">
                                        {{ $p['name'] }}
                                    </h4>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-bold text-emerald-600">
                                            {{ number_format($p['price'], 0, '.', ' ') }}
                                        </span>
                                        <span class="text-xs text-slate-500">сум</span>
                                    </div>
                                </div>

                                {{-- 🎨 ЦВЕТА --}}
                                @if (!empty($p['colors']))
                                    <div class="mb-3 flex flex-wrap gap-1.5">
                                        @foreach ($p['colors'] as $c)
                                            @php $isSelectedColor = ($selectedColor[$p['id']] ?? null) === $c['id']; @endphp
                                            <button type="button" 
                                                wire:click="selectColor({{ $p['id'] }}, {{ $c['id'] }})"
                                                class="inline-flex items-center gap-1.5 rounded-lg border-2 px-2.5 py-1 text-xs font-medium transition-all {{ $isSelectedColor ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'border-slate-200 hover:border-slate-400 hover:bg-slate-50' }}">
                                                <span class="inline-block w-3 h-3 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 {{ $isSelectedColor ? 'ring-2 ring-white' : '' }}"></span>
                                                {{ $c['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                @php
                                    $currentColorId = $selectedColor[$p['id']] ?? null;
                                    $currentColorName = $currentColorId ? (collect($p['colors'])->firstWhere('id', $currentColorId)['name'] ?? null) : null;
                                    $currentGroup = $currentColorName ? collect($p['matrix'] ?? [])->firstWhere('color', $currentColorName) : null;
                                    $selSizeId = $selectedSize[$p['id']] ?? null;
                                    $selSizeName = $selSizeId ? (collect($p['sizes'])->firstWhere('id', $selSizeId)['name'] ?? null) : null;
                                    $selRow = ($currentGroup && $selSizeName) ? collect($currentGroup['sizes'])->firstWhere('name', $selSizeName) : null;
                                    $canAdd = $currentColorId && $selSizeId;
                                @endphp

                                {{-- 📏 РАЗМЕРЫ --}}
                                @if ($currentGroup)
                                    <div class="mb-3 flex flex-wrap gap-1.5">
                                        @foreach ($currentGroup['sizes'] as $s)
                                            @php
                                                $sizeIdForBtn = collect($p['sizes'])->firstWhere('name', $s['name'])['id'] ?? null;
                                                $disabled = (int) ($s['stock'] ?? 0) <= 0 || !$sizeIdForBtn;
                                                $isSelectedSize = $selSizeId === $sizeIdForBtn;
                                            @endphp
                                            <button type="button"
                                                @if(!$disabled) wire:click="selectSize({{ $p['id'] }}, {{ $sizeIdForBtn }})" @endif
                                                class="inline-flex items-center gap-1 rounded-lg border-2 px-2.5 py-1 text-xs font-medium transition-all
                                                       {{ $disabled ? 'border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed opacity-50' : ($isSelectedSize ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'border-slate-200 hover:border-emerald-500 hover:bg-emerald-50') }}"
                                                title="{{ $s['name'] }} • остаток: {{ (int)($s['stock'] ?? 0) }}"
                                                @if($disabled) disabled @endif>
                                                <span class="font-bold">{{ $s['name'] }}</span>
                                                <span class="text-[10px] opacity-70">({{ (int)($s['stock'] ?? 0) }})</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- 📊 МИНИ-ИНФО --}}
                                @if($selRow)
                                    <div class="mb-3 p-2 rounded-lg bg-slate-50 text-[11px] text-slate-600 space-y-1">
                                        @if(!empty($selRow['sku']))
                                            <div><span class="font-medium">SKU:</span> <code class="font-mono text-slate-900">{{ $selRow['sku'] }}</code></div>
                                        @endif
                                        @if(!empty($selRow['barcode']))
                                            <div><span class="font-medium">Barcode:</span> <code class="font-mono text-slate-900">{{ $selRow['barcode'] }}</code></div>
                                        @endif
                                        <div><span class="font-medium">Остаток:</span> <span class="font-bold text-emerald-600">{{ (int)($selRow['stock'] ?? 0) }} шт</span></div>
                                    </div>
                                @endif

                                {{-- ➕ КНОПКА ДОБАВИТЬ --}}
                                <button type="button"
                                    @if($canAdd) wire:click="addToCart({{ $p['id'] }}, {{ $selSizeId ?? 'null' }}, {{ $currentColorId ?? 'null' }})" @endif
                                    class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all shadow-md hover:shadow-lg
                                           {{ $canAdd ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white hover:from-emerald-600 hover:to-emerald-700' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}"
                                    @if(!$canAdd) disabled @endif>
                                    {{ $canAdd ? '✓ Добавить в чек' : '⚠ Выберите опции' }}
                                </button>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 📜 ИСТОРИЯ ПРОДАЖ --}}
            @include('filament.pages.pos.partials.recent-orders', ['recent' => $this->recent])
        </div>

        {{-- ========== ПРАВАЯ КОЛОНКА: ЧЕК ========== --}}
        <div class="lg:col-span-4 space-y-5">

            {{-- 🎛 РЕЖИМ ОПЕРАЦИИ --}}
            <div class="rounded-2xl bg-white shadow-premium p-5">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    Режим операции
                </h3>

                <div class="grid grid-cols-3 gap-2">
                    <button type="button" wire:click="setMode('sale')"
                        class="px-4 py-3 rounded-xl font-semibold text-sm transition-all
                               {{ $mode === 'sale' ? 'bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-lg' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Продажа</span>
                        </div>
                    </button>

                    <button type="button" wire:click="setMode('return')"
                        class="px-4 py-3 rounded-xl font-semibold text-sm transition-all
                               {{ $mode === 'return' ? 'bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <span>Возврат</span>
                        </div>
                    </button>

                    <button type="button" wire:click="setMode('exchange')"
                        class="px-4 py-3 rounded-xl font-semibold text-sm transition-all
                               {{ $mode === 'exchange' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>Обмен</span>
                        </div>
                    </button>
                </div>

                {{-- 🔄 БЛОК ВОЗВРАТА --}}
                @if(in_array($mode, ['return', 'exchange']))
                    <div class="mt-4 p-4 rounded-xl {{ $mode === 'return' ? 'bg-amber-50 border-2 border-amber-200' : 'bg-emerald-50 border-2 border-emerald-200' }} animate-slide-in">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-lg {{ $mode === 'return' ? 'bg-amber-500' : 'bg-emerald-500' }} flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">Поиск исходного чека</div>
                                <div class="text-xs text-slate-600">Введите номер чека</div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <input type="text" 
                                wire:model.defer="originalNumber" 
                                placeholder="20251008123456"
                                class="block w-full rounded-xl border-2 {{ $mode === 'return' ? 'border-amber-300 focus:border-amber-500' : 'border-emerald-300 focus:border-emerald-500' }} focus:ring-0 font-mono text-sm">
                            <button wire:click="loadOriginal" type="button"
                                class="px-6 rounded-xl font-semibold text-sm shadow-md transition-all hover-lift
                                       {{ $mode === 'return' ? 'bg-gradient-to-r from-amber-500 to-amber-600' : 'bg-gradient-to-r from-emerald-500 to-emerald-600' }} text-white">
                                Найти
                            </button>
                        </div>

                        {{-- ПОЗИЦИИ ВОЗВРАТА --}}
                        @if(!empty($returnLines))
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="font-semibold text-slate-900">Позиции возврата</div>
                                    <span class="px-2 py-1 rounded-lg bg-white text-xs font-bold">Чек #{{ $originalGroupId }}</span>
                                </div>

                                <div class="max-h-[280px] overflow-auto space-y-2">
                                    @foreach($returnLines as $idx => $line)
                                        <div class="p-3 rounded-xl bg-white border-2 border-slate-100">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0 flex-1">
                                                    <div class="font-semibold text-sm truncate">{{ $line['name'] }}</div>
                                                    <div class="mt-1 flex flex-wrap gap-1.5 text-[11px]">
                                                        <span class="px-2 py-0.5 rounded bg-slate-100">💰 {{ number_format($line['price'], 0, '.', ' ') }}</span>
                                                        <span class="px-2 py-0.5 rounded bg-slate-100">📦 Макс: {{ $line['max'] }}</span>
                                                        @if(!empty($line['size_name']))
                                                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700">{{ $line['size_name'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 font-bold hover:bg-slate-200"
                                                        wire:click="$set('returnLines.{{ $idx }}.count', max(0, (int)($returnLines[{{ $idx }}]['count'] ?? 0) - 1))">−</button>
                                                    
                                                    <input type="number" min="0" max="{{ (int)($line['max'] ?? 0) }}"
                                                        class="w-14 text-center rounded-lg border-2 border-slate-200 focus:border-emerald-500 focus:ring-0 font-bold"
                                                        wire:model.lazy="returnLines.{{ $idx }}.count">
                                                    
                                                    <button type="button"
                                                        class="w-8 h-8 rounded-lg bg-emerald-500 text-white font-bold hover:bg-emerald-600"
                                                        wire:click="$set('returnLines.{{ $idx }}.count', min((int)($returnLines[{{ $idx }}]['max'] ?? 0), (int)($returnLines[{{ $idx }}]['count'] ?? 0) + 1))">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button"
                                    class="w-full py-3 rounded-xl font-bold shadow-lg transition-all hover-lift
                                           {{ $this->getCanSubmitReturnProperty() ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}"
                                    wire:click="submitReturn" 
                                    @disabled(!$this->getCanSubmitReturnProperty())>
                                    ✓ Оформить {{ $mode === 'return' ? 'возврат' : 'обмен' }}
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ⚙ ПАРАМЕТРЫ --}}
            <div class="rounded-2xl bg-white shadow-premium p-5">
                <h3 class="font-bold text-slate-900 mb-4">⚙️ Параметры продажи</h3>

                <div class="space-y-4">
                    {{-- Клиент --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">👤 Телефон клиента</label>
                        <div class="flex gap-2">
                            <input type="tel" wire:model.defer="customerPhone"
                                class="block w-full rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-0"
                                placeholder="+998 90 123 45 67">
                            <button type="button" wire:click="findCustomerByPhone"
                                class="px-4 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800">Найти</button>
                        </div>
                        @if ($customerId)
                            <div class="mt-2 p-2 rounded-lg bg-emerald-50 text-sm text-emerald-700">
                                ✓ {{ $customerName ?? 'Клиент' }}
                                <button type="button" wire:click="clearCustomer" class="ml-2 underline">Сбросить</button>
                            </div>
                        @endif
                    </div>

                    {{-- Оплата --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">💳 Способ оплаты</label>
                        <select wire:model="paymentMethod"
                            class="block w-full rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-0">
                            <option value="cash">💵 Наличные</option>
                            <option value="card">💳 Карта</option>
                            <option value="mixed">🔄 Смешанная</option>
                        </select>
                    </div>

                    {{-- Склад --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">🏪 Склад / Магазин</label>
                        <select wire:model="locationId"
                            class="block w-full rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-0">
                            @foreach ($this->locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Комментарий --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">📝 Комментарий</label>
                        <textarea wire:model.defer="comment" rows="2"
                            class="block w-full rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-0"
                            placeholder="Комментарий к чеку"></textarea>
                    </div>
                </div>
            </div>

            {{-- 🧾 ЧЕК --}}
            <div class="rounded-2xl bg-white shadow-premium overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-900 to-slate-800">
                    <h3 class="text-lg font-bold text-white">🧾 Чек</h3>
                </div>

                @if (empty($this->cart))
                    <div class="p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-slate-500 font-medium">Чек пуст</p>
                        <p class="text-sm text-slate-400 mt-1">Отсканируйте товар</p>
                    </div>
                @else
                    <div class="max-h-[50vh] overflow-auto divide-y divide-slate-100">
                        @foreach ($this->cart as $idx => $it)
                            <div class="p-4 grid grid-cols-[80px,1fr] gap-3 hover:bg-slate-50 transition-colors">
                                {{-- Фото --}}
                                <div class="rounded-lg bg-slate-100 overflow-hidden" style="width:80px;height:80px;">
                                    @if (!empty($it['image']))
                                        <img src="{{ $it['image'] }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full grid place-items-center text-slate-300 text-xs">IMG</div>
                                    @endif
                                </div>

                                {{-- Инфо --}}
                                <div class="min-w-0 flex flex-col">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="font-semibold text-sm text-slate-900 truncate">{{ $it['name'] }}</div>
                                        <button class="text-slate-400 hover:text-red-500 text-lg leading-none flex-shrink-0"
                                            wire:click="remove({{ $idx }})" title="Удалить">✕</button>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-1.5 text-[11px] mb-3">
                                        @if(!empty($it['size_name']))
                                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-medium">📏 {{ $it['size_name'] }}</span>
                                        @endif
                                        @if(!empty($it['color_name']))
                                            <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-medium">🎨 {{ $it['color_name'] }}</span>
                                        @endif
                                        @if(!empty($it['sku']))
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono">{{ $it['sku'] }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between mt-auto">
                                        {{-- Количество --}}
                                        <div class="flex items-center gap-2">
                                            <button type="button" wire:click="dec({{ $idx }})"
                                                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">−</button>
                                            <div class="w-10 text-center font-bold text-slate-900">{{ $it['qty'] }}</div>
                                            <button type="button" wire:click="inc({{ $idx }})"
                                                class="w-8 h-8 rounded-lg bg-emerald-500 text-white font-bold hover:bg-emerald-600 transition-colors">+</button>
                                        </div>

                                        {{-- Сумма --}}
                                        <div class="text-right">
                                            <div class="font-bold text-base text-emerald-600">
                                                {{ number_format($it['price'] * $it['qty'], 0, '.', ' ') }}
                                            </div>
                                            <div class="text-[10px] text-slate-500">
                                                {{ number_format($it['price'], 0, '.', ' ') }} × {{ $it['qty'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ИТОГИ --}}
                    <div class="p-5 space-y-3 bg-gradient-to-br from-slate-50 to-white border-t-2 border-slate-100">
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>Подытог</span>
                            <span class="font-semibold">{{ number_format($this->subtotal(), 0, '.', ' ') }} сум</span>
                        </div>
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>Скидка</span>
                            <span class="font-semibold">0 сум</span>
                        </div>
                        <div class="pt-3 border-t-2 border-slate-200 flex justify-between text-lg">
                            <span class="font-bold text-slate-900">Итого к оплате</span>
                            <span class="font-bold text-emerald-600">{{ number_format($this->total(), 0, '.', ' ') }} сум</span>
                        </div>

                        {{-- КНОПКИ --}}
                        <div class="grid grid-cols-2 gap-3 pt-3">
                            <button type="button" wire:click="clearCart"
                                class="py-3 rounded-xl bg-slate-100 text-slate-900 font-bold hover:bg-slate-200 transition-all hover-lift">
                                🗑️ Очистить
                            </button>

                            <button type="button" wire:click="checkout"
                                class="py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold shadow-lg hover:shadow-xl hover:from-emerald-600 hover:to-emerald-700 transition-all hover-lift"
                                @disabled(empty($this->cart))>
                                💳 Оплатить
                            </button>
                    
@if(!empty($lastOrderNumber))
<button type="button" onclick="printReceipt()"
    class="py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all hover-lift flex items-center justify-center gap-2">
    🖨️ Распечатать чек #{{ $lastOrderNumber }}
</button>
@endif
                            
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- МОДАЛЬНОЕ ОКНО ДЕТАЛЕЙ ПРОДАЖИ --}}
    @if($this->showRecentItemModal)
        <div class="fixed inset-0 z-[999] flex items-center justify-center p-4 animate-slide-in">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeRecentItemModal"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">
                {{-- Заголовок --}}
                <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 flex items-center justify-between">
                    <div class="text-lg font-bold text-white">📋 Детали продажи</div>
                    <button class="text-white/80 hover:text-white text-2xl leading-none" wire:click="closeRecentItemModal">✕</button>
                </div>

                {{-- Контент --}}
                <div class="p-6">
                    <div class="grid grid-cols-[100px,1fr] gap-4 mb-5">
                        {{-- Фото --}}
                        <div class="rounded-xl bg-slate-100 overflow-hidden" style="width:100px;height:100px;">
                            @if(!empty($this->recentItem['image']))
                                <img src="{{ $this->recentItem['image'] }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="w-full h-full grid place-items-center text-slate-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Информация --}}
                        <div class="min-w-0">
                            <h4 class="font-bold text-lg text-slate-900 mb-2">
                                {{ $this->recentItem['name'] ?? 'Без названия' }}
                            </h4>

                            <div class="space-y-1.5 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500">ID товара:</span>
                                    <span class="font-semibold">{{ $this->recentItem['product_id'] ?? '—' }}</span>
                                </div>
                                @if(!empty($this->recentItem['size_name']))
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500">Размер:</span>
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-medium text-xs">
                                            {{ $this->recentItem['size_name'] }}
                                        </span>
                                    </div>
                                @endif
                                @if(!empty($this->recentItem['color_name']))
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500">Цвет:</span>
                                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-medium text-xs">
                                            {{ $this->recentItem['color_name'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- SKU и Barcode --}}
                    @if(!empty($this->recentItem['sku']) || !empty($this->recentItem['barcode']))
                        <div class="p-3 rounded-xl bg-slate-50 mb-5 space-y-2 text-sm">
                            @if(!empty($this->recentItem['sku']))
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">SKU:</span>
                                    <code class="font-mono font-semibold text-slate-900">{{ $this->recentItem['sku'] }}</code>
                                </div>
                            @endif
                            @if(!empty($this->recentItem['barcode']))
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Barcode:</span>
                                    <code class="font-mono font-semibold text-slate-900">{{ $this->recentItem['barcode'] }}</code>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Расчёт --}}
                    <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100/50 border-2 border-emerald-200 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-700">Цена за единицу:</span>
                            <span class="font-bold text-slate-900">
                                {{ number_format((int)($this->recentItem['price'] ?? 0), 0, '.', ' ') }} сум
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-700">Количество:</span>
                            <span class="font-bold text-slate-900">{{ (int)($this->recentItem['count'] ?? 1) }} шт</span>
                        </div>
                        <div class="pt-2 border-t-2 border-emerald-300 flex justify-between">
                            <span class="font-bold text-slate-900">Итого:</span>
                            @php
                                $lineTotal = (int)($this->recentItem['price'] ?? 0) * (int)($this->recentItem['count'] ?? 1);
                            @endphp
                            <span class="font-bold text-xl text-emerald-600">
                                {{ number_format($lineTotal, 0, '.', ' ') }} сум
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Кнопка закрыть --}}
                <div class="px-6 py-4 bg-slate-50 border-t flex justify-end">
                    <button type="button" 
                        class="px-6 py-2.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors"
                        wire:click="closeRecentItemModal">
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>