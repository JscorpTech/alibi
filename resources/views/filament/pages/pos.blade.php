

<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Левая колонка --}}
        <div class="lg:col-span-8 space-y-4">

            {{-- Поиск/скан --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="flex items-center gap-3">
                    <input type="text" wire:model.debounce.300ms="barcode" wire:keydown.enter.prevent="scan" autofocus
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-400 focus:ring-0"
                        placeholder="Сканируйте штрихкод или введите код/название и нажмите Enter" />
                    <button type="button" wire:click="scan"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2 bg-gray-900 text-white text-sm font-medium hover:bg-gray-800">Добавить</button>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Сканер (barcode / sku / id) — сразу в чек. По имени — покажем варианты ниже.
                </p>
            </div>

            {{-- РЕЗУЛЬТАТЫ ПО ИМЕНИ --}}
            @if (!empty($this->results))
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($this->results as $p)
                            <div
                                class="group relative rounded-2xl border border-gray-100 bg-white p-3 shadow-sm transition-all hover:shadow-md">
                                {{-- 📸 Фото товара --}}
                                <div class="w-full aspect-[4/5] rounded-xl overflow-hidden bg-gray-100">
                                    @if (!empty($p['image']))
                                        <img src="{{ $p['image'] }}" alt=""
                                            class="h-full w-full object-cover object-center transition-transform duration-300 group-hover:scale-[1.03]"
                                            loading="lazy" decoding="async">
                                    @else
                                        <div class="grid h-full w-full place-items-center text-gray-300 text-xs">IMG</div>
                                    @endif
                                </div>

                                {{-- 🏷 Название + цена --}}
                                <div class="mt-2">
                                    <div class="text-sm font-medium truncate" title="{{ $p['name'] }}">
                                        {{ $p['name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ number_format($p['price'], 0, '.', ' ') }} сум
                                    </div>
                                </div>

                                {{-- Цвета --}}
                                {{-- Цвета --}}
                                @if (!empty($p['colors']))
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @foreach ($p['colors'] as $c)
                                            @php
                                                $isSelectedColor = ($selectedColor[$p['id']] ?? null) === $c['id'];
                                            @endphp
                                            <button type="button" wire:click="selectColor({{ $p['id'] }}, {{ $c['id'] }})"
                                                class="inline-flex items-center gap-1 rounded-md border px-2 py-[2px] text-[11px] {{ $isSelectedColor ? 'bg-gray-900 text-white border-gray-900' : 'hover:bg-gray-50' }}">
                                                <span class="inline-block w-[10px] h-[10px] rounded-full bg-gray-300"></span>
                                                {{ $c['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                @php
                                    // какие цвет/группа сейчас выбраны
                                    $currentColorId = $selectedColor[$p['id']] ?? null;
                                    $currentColorName = $currentColorId
                                        ? (collect($p['colors'])->firstWhere('id', $currentColorId)['name'] ?? null)
                                        : null;

                                    $currentGroup = $currentColorName
                                        ? collect($p['matrix'] ?? [])->firstWhere('color', $currentColorName)
                                        : null;

                                    // выбранный размер (id из справочника Sizes)
                                    $selSizeId = $selectedSize[$p['id']] ?? null;

                                    // для отображения SKU/Barcode и stock по выбранному размеру берём строку из matrix
                                    $selSizeName = $selSizeId
                                        ? (collect($p['sizes'])->firstWhere('id', $selSizeId)['name'] ?? null)
                                        : null;

                                    $selRow = ($currentGroup && $selSizeName)
                                        ? collect($currentGroup['sizes'])->firstWhere('name', $selSizeName)
                                        : null;

                                    $canAdd = $currentColorId && $selSizeId; // оба выбраны
                                @endphp

                                {{-- Размеры ПОД выбранным цветом (из matrix) --}}
@if ($currentGroup)
    <div class="mt-2 flex flex-wrap gap-1.5">
        @foreach ($currentGroup['sizes'] as $s)
            @php
                // получаем size_id по имени из $p['sizes']
                $sizeIdForBtn = collect($p['sizes'])->firstWhere('name', $s['name'])['id'] ?? null;
                $disabled = (int) ($s['stock'] ?? 0) <= 0 || !$sizeIdForBtn;
                $isSelectedSize = $selSizeId === $sizeIdForBtn;
            @endphp
            <button type="button"
                @if(!$disabled) wire:click="selectSize({{ $p['id'] }}, {{ $sizeIdForBtn }})" @endif
                class="inline-flex items-center gap-1 rounded-md border px-2 py-[2px] text-[11px]
                       {{ $disabled ? 'cursor-not-allowed opacity-40' : ($isSelectedSize ? 'bg-gray-900 text-white border-gray-900' : 'hover:bg-gray-50') }}"
                title="{{ $s['name'] }} • ост: {{ (int)($s['stock'] ?? 0) }}"
                @if($disabled) disabled @endif>
                <span class="font-medium">{{ $s['name'] }}</span>
                <span class="text-[10px] text-gray-400">({{ (int)($s['stock'] ?? 0) }})</span>
            </button>
        @endforeach
    </div>
@endif

                                

                                {{-- Мини-инфо по выбранной комбинации (SKU / Barcode / Остаток) --}}
@if($selRow)
    <div class="mt-2 text-[11px] text-gray-600 flex flex-wrap gap-3">
        @if(!empty($selRow['sku']))
            <span>SKU: <span class="font-mono">{{ $selRow['sku'] }}</span></span>
        @endif
        @if(!empty($selRow['barcode']))
            <span>Barcode: <span class="font-mono">{{ $selRow['barcode'] }}</span></span>
        @endif
        <span>Остаток: <span class="font-semibold">{{ (int)($selRow['stock'] ?? 0) }}</span></span>
    </div>
@endif

{{-- Кнопка "Добавить" — активна только если выбран и цвет, и размер --}}
<div class="mt-3 flex justify-end">
    <button type="button"
        @if($canAdd)
            wire:click="addToCart({{ $p['id'] }}, {{ $selSizeId ?? 'null' }}, {{ $currentColorId ?? 'null' }})"
        @endif
        class="grid h-8 px-3 place-items-center rounded-xl text-sm transition
               {{ $canAdd ? 'bg-gray-900 text-white hover:bg-gray-800' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}"
        @if(!$canAdd) title="Выберите цвет и размер" @endif
        @if(!$canAdd) disabled @endif>
        Добавить
    </button>
</div>

                                {{-- 📦 Суммарный остаток --}}
                                @if(!empty($p['qty_total']))
                                    <div class="mt-1 text-[11px] text-gray-600">
                                        Количество: <span class="font-medium">{{ $p['qty_total'] }}</span>
                                    </div>
                                @endif

                                {{-- 📏 Размеры / добавление --}}
                                @if (!empty($p['sizes']))
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach ($p['sizes'] as $s)
                                            @php $disabled = isset($s['stock']) && (int) $s['stock'] <= 0; @endphp
                                            <button type="button" wire:click="addToCart({{ $p['id'] }}, {{ $s['id'] }})"
                                                class="inline-flex items-center gap-1 rounded-md border px-2 py-[2px] text-[11px] {{ $disabled ? 'cursor-not-allowed opacity-40' : 'hover:bg-gray-50' }}"
                                                title="{{ $s['name'] }}@isset($s['stock']) • ост: {{ $s['stock'] }} @endisset"
                                                @if($disabled) disabled @endif>
                                                <span class="font-medium">{{ $s['name'] }}</span>
                                                @isset($s['stock'])
                                                    <span class="text-[10px] text-gray-400">({{ $s['stock'] }})</span>
                                                @endisset
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mt-3 flex justify-end">
                                        <button type="button" wire:click="addToCart({{ $p['id'] }})"
                                            class="grid h-8 w-8 place-items-center rounded-xl bg-gray-900 text-white text-sm transition hover:bg-gray-800"
                                            title="Добавить в чек">+</button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <x-filament::section>
                <div class="text-sm text-gray-500">
                    Каталог карточек добавим позже. Сейчас товар попадает в чек сразу после скана.
                </div>
            </x-filament::section>

            {{-- 🔹 История последних продаж --}}
            @include('filament.pages.pos.partials.recent-orders', ['recent' => $this->recent])
        </div>

        {{-- Правая колонка --}}
        <div class="lg:col-span-4 space-y-4">

            {{-- 🔹 Режим операции: Продажа / Возврат / Обмен --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h3 class="font-semibold text-sm">Режим</h3>

                    <div class="inline-flex gap-2">
                        <button type="button" wire:click="setMode('sale')"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium border
                       {{ $mode === 'sale' ? 'bg-gray-900 text-white border-gray-900' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}">
                            Продажа
                        </button>
                        <button type="button" wire:click="setMode('return')"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium border
                       {{ $mode === 'return' ? 'bg-amber-600 text-white border-amber-600' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}">
                            Возврат
                        </button>
                        <button type="button" wire:click="setMode('exchange')"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium border
                       {{ $mode === 'exchange' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}">
                            Обмен
                        </button>
                    </div>
                </div>

                {{-- Если возврат или обмен — показать блок поиска исходного чека --}}
                @if(in_array($mode, ['return', 'exchange']))
                    <div class="mt-3 border-t pt-3 space-y-2">
                        <div class="text-sm text-gray-600">Введите номер исходного чека:</div>
                        <div class="flex gap-2">
                            <input type="text" wire:model.defer="originalNumber" placeholder="Например: 20251008123456"
                                class="block w-full rounded-lg border-gray-300 focus:border-gray-400 focus:ring-0">
                            <button wire:click="loadOriginal" type="button"
                                class="rounded-lg bg-gray-900 text-white px-4 py-2 text-sm font-medium hover:bg-gray-800">
                                Найти
                            </button>
                        </div>
                    </div>

                    {{-- Если нашли исходный чек --}}
                    @if(!empty($returnLines))
                        <div class="mt-4 border-t pt-3 space-y-2">
                            <div class="text-sm font-semibold">Позиции исходного чека</div>

                            <div class="max-h-[240px] overflow-auto divide-y divide-gray-100">
                                @foreach($returnLines as $idx => $line)
                                    <div class="flex items-center justify-between gap-3 py-2">
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium truncate">{{ $line['name'] }}</div>
                                            <div class="text-xs text-gray-500">
                                                Цена: {{ number_format($line['price'], 0, '.', ' ') }} сум
                                                • Макс: {{ $line['max'] }}
                                            </div>
                                        </div>
                                        <input type="number" min="0" max="{{ $line['max'] }}"
                                            wire:model="returnLines.{{ $idx }}.count"
                                            class="w-16 rounded-lg border-gray-300 text-center text-sm focus:border-gray-400 focus:ring-0">
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-xs text-gray-400">Введите количество, которое клиент возвращает.</div>
                        </div>
                    @endif


                @endif

            </div>
            {{-- 🔄 Блок возврата: показываем, если включён режим return --}}
            @if($this->mode === 'return')
                <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold">Возврат по чеку</div>
                        @if($this->originalGroupId)
                            <div class="text-xs text-gray-600">Чек ID: {{ $this->originalGroupId }}</div>
                        @endif
                    </div>

                    @if(empty($this->returnLines))
                        <div class="text-sm text-gray-600">
                            Введите номер чека и нажмите «Найти», чтобы выбрать позиции для возврата.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($this->returnLines as $i => $row)
                                <div class="flex items-center justify-between gap-3 rounded-lg border bg-white px-3 py-2">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium truncate">
                                            {{ $row['name'] ?? ('#' . $row['product_id']) }}
                                        </div>
                                        <div class="text-[11px] text-gray-500">
                                            Цена: {{ number_format($row['price'] ?? 0, 0, '.', ' ') }} сум • Макс:
                                            {{ $row['max'] ?? 0 }}
                                            @if(!empty($row['size_name'])) • {{ $row['size_name'] }} @endif
                                            @if(!empty($row['color_name'])) • {{ $row['color_name'] }} @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            class="inline-flex items-center justify-center rounded-md px-2.5 h-7 bg-gray-100 text-gray-700 text-xs hover:bg-gray-200"
                                            wire:click="$set('returnLines.{{ $i }}.count', max(0, (int)($returnLines[{{ $i }}]['count'] ?? 0) - 1))">−</button>

                                        <input type="number" min="0" max="{{ (int) ($row['max'] ?? 0) }}"
                                            class="w-16 rounded-md border-gray-300 text-center text-sm"
                                            wire:model.lazy="returnLines.{{ $i }}.count">

                                        <button type="button"
                                            class="inline-flex items-center justify-center rounded-md px-2.5 h-7 bg-gray-100 text-gray-700 text-xs hover:bg-gray-200"
                                            wire:click="$set('returnLines.{{ $i }}.count', min((int)($returnLines[{{ $i }}]['max'] ?? 0), (int)($returnLines[{{ $i }}]['count'] ?? 0) + 1))">+</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button"
                                class="inline-flex items-center justify-center rounded-lg px-4 py-2 bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 disabled:opacity-50"
                                wire:click="submitReturn" @disabled(!$this->getCanSubmitReturnProperty())>
                                Оформить возврат
                            </button>
                        </div>
                    @endif
                </div>
            @endif
            {{-- 🔧 Параметры продажи --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="grid grid-cols-1 gap-4">

                    {{-- Покупатель --}}
                    {{-- Покупатель: телефон -> найти/создать --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Телефон клиента</label>
                        <div class="mt-1 flex gap-2">
                            <input type="tel" wire:model.defer="customerPhone"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-400 focus:ring-0"
                                placeholder="+998 90 123 45 67">
                            <button type="button" wire:click="findCustomerByPhone"
                                class="inline-flex items-center justify-center rounded-lg px-3 bg-gray-900 text-white text-sm hover:bg-gray-800">Найти</button>
                        </div>

                        {{-- Статус поиска / выбранный клиент --}}
                        @if ($customerId)
                            <div class="mt-1 text-xs text-emerald-700">
                                Клиент выбран (ID: {{ $customerId }}) — {{ $customerName ?? '' }}
                                <button type="button" wire:click="clearCustomer" class="ml-2 underline">Сбросить</button>
                            </div>
                        @elseif(!empty($customerPhone))
                            <div class="mt-1 text-xs text-amber-700">
                                Клиент с таким телефоном не найден.
                                <button type="button" wire:click="createGuestCustomer"
                                    class="ml-1 underline">Создать</button>
                                или проведём чек без клиента.
                            </div>
                        @endif
                    </div>
                    {{-- Способ оплаты --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Способ оплаты</label>
                        <select wire:model="paymentMethod"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-400 focus:ring-0">
                            <option value="cash">Наличные</option>
                            <option value="card">Карта</option>
                            <option value="mixed">Смешанная</option>
                        </select>
                    </div>

                    {{-- Склад / Магазин --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Склад / Магазин</label>
                        <select wire:model="locationId"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-400 focus:ring-0">
                            @foreach ($this->locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Комментарий --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Комментарий</label>
                        <textarea wire:model.defer="comment" rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-400 focus:ring-0"
                            placeholder="Комментарий к чеку"></textarea>
                    </div>

                </div>
            </div>

            {{-- Detail Transaction --}}
            <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                <div class="px-4 py-3 border-b">
                    <h3 class="text-base font-semibold">Detail Transaction</h3>
                </div>

                @if (empty($this->cart))
                    <div class="p-4 text-gray-500">Пусто. Отсканируйте товар.</div>
                @else
                    <div class="max-h-[60vh] overflow-auto divide-y">
                        @foreach ($this->cart as $idx => $it)
                            <div class="p-3 grid grid-cols-[80px,1fr] items-start gap-3">
                                {{-- 📸 Фото --}}
                                <div class="rounded-lg bg-gray-100 overflow-hidden flex-shrink-0"
                                    style="width: 80px; height: 80px;">
                                    @if (!empty($it['image']))
                                        <img src="{{ $it['image'] }}" class="w-full h-full object-cover object-center" alt="">
                                    @else
                                        <div class="w-full h-full grid place-items-center text-gray-400 text-xs">IMG</div>
                                    @endif
                                </div>

                                {{-- 🧾 Информация + управление --}}
                                <div class="min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="truncate font-medium text-sm text-gray-900">{{ $it['name'] }}</div>
                                        <button class="text-gray-400 hover:text-red-500 text-lg leading-none"
                                            wire:click="remove({{ $idx }})" title="Удалить">✕</button>
                                    </div>

                                    <div class="mt-1 flex flex-wrap items-center gap-1 text-[11px] leading-tight text-gray-600">
                                        @if(!empty($it['size_name']))
                                            <span class="px-1.5 py-[1px] rounded bg-gray-100 text-gray-700">Размер:
                                                {{ $it['size_name'] }}</span>
                                        @endif
                                        @if(!empty($it['sku']))
                                            <span class="px-1.5 py-[1px] rounded bg-gray-100 text-gray-700">SKU:
                                                {{ $it['sku'] }}</span>
                                        @endif
                                        <span class="text-gray-400">ID: {{ $it['id'] }}</span>
                                        <span class="text-gray-400">Цена: {{ number_format($it['price'], 0, '.', ' ') }}</span>
                                    </div>
                                    @if(!empty($it['color_name']))
                                        <span class="px-1.5 py-[1px] rounded bg-gray-100 text-gray-700">
                                            Цвет: {{ $it['color_name'] }}
                                        </span>
                                    @endif

                                    <div class="mt-2 flex items-center justify-between">

                                        {{-- Количество --}}
                                        <div class="flex items-center gap-2">
                                            <button type="button" wire:click="dec({{ $idx }})"
                                                class="inline-flex items-center justify-center rounded-md px-2.5 h-7 bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">−</button>
                                            <div class="w-8 text-center font-medium">{{ $it['qty'] }}</div>
                                            <button type="button" wire:click="inc({{ $idx }})"
                                                class="inline-flex items-center justify-center rounded-md px-2.5 h-7 bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">+</button>
                                        </div>

                                        {{-- Итог по позиции --}}
                                        <div class="text-right">
                                            <div class="font-semibold text-sm text-gray-900">
                                                {{ number_format($it['price'] * $it['qty'], 0, '.', ' ') }}
                                            </div>
                                            <div class="text-[11px] text-gray-500">
                                                = {{ number_format($it['price'], 0, '.', ' ') }} × {{ $it['qty'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 space-y-2 border-t bg-gray-50">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Sub-Total</span>
                            <span>{{ number_format($this->subtotal(), 0, '.', ' ') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span>0</span>
                        </div>
                        <div class="flex justify-between text-base font-semibold pt-2 border-t">
                            <span>Total Payment</span>
                            <span>{{ number_format($this->total(), 0, '.', ' ') }}</span>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="button" wire:click="clearCart"
                                class="flex-1 inline-flex items-center justify-center rounded-lg px-4 py-2 bg-gray-100 text-gray-900 text-sm font-medium hover:bg-gray-200">Очистить</button>

                            <button type="button" wire:click="checkout"
                                class="flex-1 inline-flex items-center justify-center rounded-lg px-4 py-2 bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 disabled:opacity-50"
                                @disabled(empty($this->cart)) {{-- если нужна полная поддержка disabled: добавь Alpine/JS
                                --}}>Оплатить</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
    @if($this->showRecentItemModal)
        <div class="fixed inset-0 z-[999]">
            <div class="absolute inset-0 bg-black/40" wire:click="closeRecentItemModal"></div>

            <div
                class="absolute inset-x-0 top-[10vh] mx-auto w-[92vw] max-w-xl rounded-2xl bg-white shadow-xl overflow-hidden">
                <div class="px-4 py-3 border-b flex items-center justify-between">
                    <div class="text-base font-semibold">Детали продажи</div>
                    <button class="text-gray-400 hover:text-gray-600" wire:click="closeRecentItemModal">✕</button>
                </div>

                <div class="p-4 grid grid-cols-[96px,1fr] gap-3">
                    <div class="rounded-lg bg-gray-100 overflow-hidden" style="width:96px; height:96px;">
                        @if(!empty($this->recentItem['image']))
                            <img src="{{ $this->recentItem['image'] }}" class="w-full h-full object-cover" alt="">
                        @else
                            <div class="w-full h-full grid place-items-center text-gray-400 text-xs">IMG</div>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="font-medium">
                            {{ $this->recentItem['name'] ?? 'Без названия' }}
                        </div>

                        <div class="mt-1 text-[12px] text-gray-600 space-x-2">
                            <span>Product ID: {{ $this->recentItem['product_id'] ?? '—' }}</span>
                            @if(!empty($this->recentItem['size_name']))
                                <span>• Размер: {{ $this->recentItem['size_name'] }}</span>
                            @endif
                            @if(!empty($this->recentItem['color_name']))
                                <span>• Цвет: {{ $this->recentItem['color_name'] }}</span>
                            @endif
                        </div>

                        @if(!empty($this->recentItem['sku']) || !empty($this->recentItem['barcode']))
                            <div class="mt-1 text-[12px] text-gray-500 space-x-3">
                                @if(!empty($this->recentItem['sku']))
                                    <span>SKU: <span class="font-mono">{{ $this->recentItem['sku'] }}</span></span>
                                @endif
                                @if(!empty($this->recentItem['barcode']))
                                    <span>Barcode: <span class="font-mono">{{ $this->recentItem['barcode'] }}</span></span>
                                @endif
                            </div>
                        @endif

                        <div class="mt-2 text-sm">
                            Цена:
                            <b>{{ number_format((int) ($this->recentItem['price'] ?? 0), 0, '.', ' ') }}</b> сум
                            × Кол-во:
                            <b>{{ (int) ($this->recentItem['count'] ?? 1) }}</b>
                        </div>

                        @php
                            $lineTotal = (int) ($this->recentItem['total'] ?? (
                                (int) ($this->recentItem['price'] ?? 0) * (int) ($this->recentItem['count'] ?? 1)
                            ));
                        @endphp
                        <div class="text-sm font-semibold">
                            Итого: {{ number_format($lineTotal, 0, '.', ' ') }} сум
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 border-t bg-gray-50 flex justify-end gap-2">
                    <button type="button" class="rounded-lg px-3 py-2 bg-gray-100 hover:bg-gray-200"
                        wire:click="closeRecentItemModal">Закрыть</button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>