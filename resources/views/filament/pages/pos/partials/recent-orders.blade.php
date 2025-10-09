<x-filament::section>
    <h3 class="text-sm font-semibold mb-2">🧾 Последние продажи POS</h3>

    @if(empty($recent))
        <div class="text-sm text-gray-500">Пока нет продаж.</div>
    @else
        <div class="space-y-3">
            @foreach($recent as $row)
                <div class="rounded-lg border border-gray-100 p-3">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <div class="text-sm font-medium">
                                Чек № {{ $row['number'] }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $row['created_at'] }} • {{ ucfirst($row['status']) }} • {{ $row['items_count'] }} поз.
                                @if(!empty($row['payment'])) • {{ $row['payment'] }} @endif
                                @if(!empty($row['customer']['name']) || !empty($row['customer']['phone']))
                                    • {{ $row['customer']['name'] ?? '' }} {{ $row['customer']['phone'] ?? '' }}
                                @endif
                            </div>
                        </div>
                        <div class="text-sm font-semibold whitespace-nowrap">
                            {{ number_format($row['total'], 0, '.', ' ') }} сум
                        </div>
                    </div>

                    {{-- позиции --}}
                    {{-- позиции --}}
                    <div class="mt-2 grid gap-2">
                        @foreach($row['items'] as $item)
                            <button type="button"
                                wire:click="openRecentItem({{ (int) $row['id'] }}, {{ (int) $item['product_id'] }}, {{ $item['size_id'] ?? 'null' }})"
                                class="flex items-center gap-2 w-full text-left hover:bg-gray-50 rounded-md p-1.5">
                                <div class="w-10 h-10 rounded-md overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full grid place-items-center text-[10px] text-gray-400">IMG</div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-medium truncate">{{ $item['name'] }}</div>
                                    <div class="text-[11px] text-gray-500">
                                        {{ number_format($item['price'], 0, '.', ' ') }} × {{ $item['count'] }}
                                        @if(!empty($item['size'])) • {{ $item['size'] }} @endif
                                        @if(!empty($item['color'])) • {{ $item['color'] }} @endif
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</x-filament::section>