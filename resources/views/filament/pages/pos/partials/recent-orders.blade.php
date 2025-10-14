<div class="rounded-2xl bg-white border-2 border-slate-100 shadow-premium overflow-hidden animate-scale-in">
    <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b-2 border-slate-100">
        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            🧾 Последние продажи POS
        </h3>
        <p class="text-sm text-slate-600 mt-1">История недавних транзакций</p>
    </div>

    @if(empty($recent))
        <div class="px-6 py-16 text-center">
            <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-slate-500 font-medium">Пока нет продаж</p>
            <p class="text-sm text-slate-400 mt-1">Начните продавать товары</p>
        </div>
    @else
        <div class="p-6 space-y-4">
            @foreach($recent as $row)
                <div class="group rounded-2xl border-2 border-slate-100 bg-white p-5 hover-lift hover:border-emerald-400 transition-all">
                    {{-- Header чека --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3">
                                <h4 class="text-base font-bold text-slate-900">
                                    Чек № {{ $row['number'] }}
                                </h4>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'success' => 'bg-green-100 text-green-700 border-green-200',
                                        'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $statusIcons = [
                                        'pending' => '⏳',
                                        'success' => '✅',
                                        'cancelled' => '❌',
                                    ];
                                    $statusColor = $statusColors[$row['status']] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    $statusIcon = $statusIcons[$row['status']] ?? '📄';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusColor }}">
                                    <span>{{ $statusIcon }}</span>
                                    <span>{{ ucfirst($row['status']) }}</span>
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-slate-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $row['created_at'] }}
                                </span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    {{ $row['items_count'] }} позиций
                                </span>
                                @if(!empty($row['payment']))
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        {{ $row['payment'] }}
                                    </span>
                                @endif
                                @if(!empty($row['customer']['name']) || !empty($row['customer']['phone']))
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $row['customer']['name'] ?? '' }} {{ $row['customer']['phone'] ?? '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <div class="text-2xl font-bold text-emerald-600">
                                {{ number_format($row['total'], 0, '.', ' ') }}
                            </div>
                            <div class="text-xs text-slate-500 font-medium">сум</div>
                            
                            {{-- ✅ KNOPKA PECHATI --}}
                            <button type="button" 
                                onclick="printOrderReceipt({{ $row['id'] }})"
                                class="mt-2 w-full px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-1">
                                🖨️ Печать
                            </button>
                        </div>
                    </div>

                    {{-- Позиции --}}
                    <div class="grid gap-2">
                        @foreach($row['items'] as $item)
                            <button type="button"
                                wire:click="openRecentItem({{ (int) $row['id'] }}, {{ (int) $item['product_id'] }}, {{ $item['size_id'] ?? 'null' }})"
                                class="flex items-center gap-3 w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 border-2 border-transparent hover:border-emerald-200 transition-all group/item">
                                
                                {{-- Фото --}}
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 shadow-sm">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" class="w-full h-full object-cover transition-transform duration-300 group-hover/item:scale-110" alt="">
                                    @else
                                        <div class="w-full h-full grid place-items-center">
                                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Инфо --}}
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-semibold text-slate-900 truncate mb-1">
                                        {{ $item['name'] }}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="font-bold text-slate-900">
                                            {{ number_format($item['price'], 0, '.', ' ') }} × {{ $item['count'] }}
                                        </span>
                                        @if(!empty($item['size']))
                                            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 font-medium">
                                                📏 {{ $item['size'] }}
                                            </span>
                                        @endif
                                        @if(!empty($item['color']))
                                            <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 font-medium">
                                                🎨 {{ $item['color'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Сумма --}}
                                <div class="flex-shrink-0 text-right">
                                    <div class="font-bold text-sm text-slate-900">
                                        {{ number_format($item['price'] * $item['count'], 0, '.', ' ') }}
                                    </div>
                                    <div class="text-xs text-slate-500">сум</div>
                                </div>

                                {{-- Стрелка --}}
                                <svg class="w-5 h-5 text-slate-400 group-hover/item:text-emerald-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ✅ SHABLON PECHATI CHEKA --}}
<div id="receipt-print-{{ $row['id'] ?? 'template' }}" class="hidden print:block">
    <style>
        @media print {
            body * { visibility: hidden; }
            #receipt-print-{{ $row['id'] ?? 'template' }}, 
            #receipt-print-{{ $row['id'] ?? 'template' }} * { visibility: visible; }
            #receipt-print-{{ $row['id'] ?? 'template' }} { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
            }
        }
    </style>
    
    <div class="w-full max-w-[80mm] mx-auto p-4 font-mono text-sm">
        <div class="text-center mb-3">
            <div class="font-bold text-lg">{{ config('app.name', 'MAGAZIN') }}</div>
            <div class="text-xs">ИНН: 123456789</div>
        </div>
        
        <div class="border-t-2 border-b-2 border-dashed border-black py-2 mb-2 text-xs">
            <div class="flex justify-between">
                <span>Чек №:</span>
                <span class="font-bold">{{ $row['number'] ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Дата:</span>
                <span>{{ $row['created_at'] ?? now()->format('d.m.Y H:i') }}</span>
            </div>
        </div>
        
        <table class="w-full text-xs mb-2">
            <thead>
                <tr class="border-b border-dashed">
                    <th class="text-left py-1">Товар</th>
                    <th class="text-center py-1">Кол</th>
                    <th class="text-right py-1">Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($row['items'] ?? [] as $item)
                <tr class="border-b border-dotted">
                    <td class="py-1">{{ $item['name'] }}</td>
                    <td class="text-center py-1">{{ $item['count'] }}</td>
                    <td class="text-right py-1">{{ number_format($item['price'] * $item['count'], 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="border-t-2 border-dashed border-black pt-2 text-sm">
            <div class="flex justify-between font-bold">
                <span>ИТОГО:</span>
                <span>{{ number_format($row['total'] ?? 0, 0, '.', ' ') }} сум</span>
            </div>
        </div>
        
        <div class="text-center text-xs mt-3">Спасибо за покупку!</div>
    </div>
</div>

<script>
function printOrderReceipt(orderId) {
    // Zagruzhaem dannie cheka cherez Livewire
    @this.loadReceiptData(orderId).then(() => {
        window.print();
    });
}
</script>