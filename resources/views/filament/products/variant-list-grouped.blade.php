{{-- resources/views/filament/products/variant-list-grouped.blade.php --}}

@php
  $axisColor = 'Color';
  $axisSize = 'Size';

  $groupsByColor = collect($rows)
    ->groupBy(fn($r) => data_get($r, "attrs.$axisColor", '—'))
    ->sortKeys()
    ->map(fn($items) => collect($items)->sortBy(fn($r) => (string) ($r['title'] ?? ''))->values());

  $stocks = $stocks ?? [];
  if (empty($stocks)) {
    foreach ($rows as $r) {
      $attrs = (array) ($r['attrs'] ?? []);
      ksort($attrs);
      $rk = !empty($r['id'])
        ? 'id:' . (int) $r['id']
        : 'attrs:' . substr(md5(json_encode($attrs, JSON_UNESCAPED_UNICODE)), 0, 12);
      $stocks[$rk] = (int) ($r['stock'] ?? 0);
    }
  }
@endphp

<div class="space-y-6" x-data="{
        stocks: @js($stocks),
        sync() {
          Object.entries(this.stocks).forEach(([k,v]) => {
            @this.set('stocks.'+k, parseInt(v)||0, false);
          });
        }
     }" x-init="
        sync();
        $el.closest('form')?.addEventListener('submit', () => sync());
     ">

  {{-- Заголовок таблицы --}}
  <div class="hidden lg:grid grid-cols-12 gap-4 px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
    <div class="col-span-5 text-xs font-semibold text-gray-600 uppercase tracking-wider">Вариант товара</div>
    <div class="col-span-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Идентификаторы</div>
    <div class="col-span-2 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Остаток</div>
    <div class="col-span-2 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Статус</div>
  </div>

  {{-- Группы по цвету --}}
  @foreach($groupsByColor as $color => $items)
    @php $count = $items->count(); @endphp

    <div x-data="{ open: true }" class="rounded-2xl border border-gray-200 bg-white shadow-none overflow-hidden">

      {{-- Шапка цвета --}}
      <div class="px-5 pt-3">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-50 border border-gray-200">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 7l9-4 9 4-9 4-9-4m0 0v10l9 4 9-4V7" />
            </svg>
          </div>

          <div class="flex-1">
            <div class="text-base font-semibold text-gray-900 uppercase leading-6">{{ $color }}</div>

            <button type="button" class="mt-1 inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-xs font-medium
                             border border-gray-200 bg-white text-gray-700 focus:outline-none" @click="open = !open">
              <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 7l9-4 9 4-9 4-9-4m0 0v10l9 4 9-4V7" />
              </svg>
              <span>{{ $count }} {{ \Illuminate\Support\Str::plural('вариант', $count) }}</span>
              <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform duration-200"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      {{-- Отступ вместо разделителя --}}
      <div class="mt-3"></div>

      {{-- Список вариантов (БЕЗ divider) --}}
      <div x-show="open" x-collapse class="bg-white">
        @foreach($items as $r)
          @php
            $attrs = (array) ($r['attrs'] ?? []);
            ksort($attrs);
            $rowKey = !empty($r['id'])
              ? 'id:' . (int) $r['id']
              : 'attrs:' . substr(md5(json_encode($attrs, JSON_UNESCAPED_UNICODE)), 0, 12);

            $currentStock = $stocks[$rowKey] ?? (int) ($r['stock'] ?? 0);
            $sizeLabel = (string) ($attrs[$axisSize] ?? '—');
          @endphp

          <div class="px-5 py-3 hover:bg-gray-50 transition">
            <div class="grid grid-cols-12 gap-4 items-center">

              {{-- Название варианта --}}
              <div class="col-span-5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-gray-100 flex items-center justify-center border border-gray-200">
                  <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7l9-4 9 4-9 4-9-4m0 0v10l9 4 9-4V7" />
                  </svg>
                </div>
                <span class="font-medium text-gray-900">{{ $sizeLabel }}</span>
              </div>

              {{-- Идентификаторы --}}
              <div class="col-span-3">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium">SKU:</span>
                    <code class="text-xs font-mono px-2 py-0.5 bg-gray-100 text-gray-800 rounded">
                          {{ $r['sku'] ?? '—' }}
                        </code>
                  </div>
                  @if(!empty($r['barcode']))
                    <div class="flex items-center gap-2">
                      <span class="text-xs text-gray-500 font-medium">Barcode:</span>
                      <code class="text-xs font-mono px-2 py-0.5 bg-gray-100 text-gray-800 rounded">
                              {{ $r['barcode'] }}
                            </code>
                    </div>
                  @endif
                </div>
              </div>

              {{-- Остаток --}}
              <div class="col-span-2 flex justify-center">
                <input type="number" min="0" :value="stocks['{{ $rowKey }}'] ?? {{ $currentStock }}" class="w-20 text-center border border-gray-200 rounded-lg px-2 py-1.5
                                  font-semibold text-gray-900 focus:border-blue-500 focus:ring-0"
                  @input="stocks['{{ $rowKey }}'] = parseInt($event.target.value) || 0" @focus="$el.select()" />
              </div>

              {{-- Статус --}}
              <div class="col-span-2 flex justify-center">
                <span :class="{
            'bg-green-50 text-green-700 border-green-200': (stocks['{{ $rowKey }}'] ?? {{ $currentStock }}) > 0,
            'bg-red-50 text-red-700 border-red-200':    (stocks['{{ $rowKey }}'] ?? {{ $currentStock }}) === 0
          }" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border">
                  <span :class="{
              'bg-green-500': (stocks['{{ $rowKey }}'] ?? {{ $currentStock }}) > 0,
              'bg-red-500':   (stocks['{{ $rowKey }}'] ?? {{ $currentStock }}) === 0
            }" class="w-2 h-2 rounded-full"></span>
                  <span
                    x-text="(stocks['{{ $rowKey }}'] ?? {{ $currentStock }}) > 0 ? 'В наличии' : 'Нет в наличии'"></span>
                </span>
              </div>

            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endforeach
</div>