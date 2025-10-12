@php
    use Illuminate\Support\Facades\Storage;

    $toUrl = function (?string $p): ?string {
        if (!$p)
            return null;
        return str_starts_with($p, 'http') ? $p : Storage::url($p);
    };
@endphp

<div class="rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-2 font-semibold bg-gray-50">Существующие варианты</div>
    @if(empty($variants))
        <div class="p-4 text-sm text-gray-500">Пока нет вариантов.</div>
    @else
        <div class="divide-y">
            @foreach($variants as $v)

                @php
                    // cover уже подготовлен в ProductResource::form()->viewData(...)
                    $src = $toUrl($v['cover'] ?? null);
                @endphp

                <div class="px-4 py-3 flex items-center gap-3">
                    <div class="w-12 h-12 rounded bg-gray-100 overflow-hidden flex-shrink-0">
                        @if($src)
                            <img src="{{ $src }}" class="w-full h-full object-cover" alt="">
                        @else
                            <div class="w-full h-full grid place-items-center text-[10px] text-gray-400">IMG</div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-medium truncate">{{ $v['title'] }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $v['attrs'] }}</div>
                        <div class="mt-1 text-xs text-gray-600">
                            • SKU: <span class="font-mono">{{ $v['sku'] ?? '—' }}</span>
                            • Barcode: <span class="font-mono">{{ $v['barcode'] ?? '—' }}</span>
                            • Остаток: <b>{{ (int) ($v['stock'] ?? 0) }}</b>
                            • {{ !empty($v['available']) ? 'Вкл.' : 'Выкл.' }}
                        </div>
                        <div class="text-[10px] text-gray-400">variants_count: {{ $variants_count ?? 'n/a' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>