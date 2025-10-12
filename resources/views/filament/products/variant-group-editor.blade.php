@php
    // ожидается $groups = [
    //   ['key'=>'21', 'items'=>[['idx'=>0,'title'=>'Size: 21 / Color: Black','sku'=>'..'], ...]],
    //   ...
    // ]
@endphp

<div class="rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-2 font-semibold bg-gray-50">
        Варианты (сгруппировано)
    </div>

    @if (empty($groups))
        <div class="p-4 text-sm text-gray-500">Пока нет вариантов.</div>
    @else
        <div class="divide-y">
            @foreach ($groups as $g)
                <div x-data="{ open: true }" class="p-0">
                    <button type="button"
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50"
                            @click="open = !open">
                        <span class="font-medium">{{ $g['key'] }}</span>
                        <span class="text-xs text-gray-500">{{ count($g['items'] ?? []) }} варианта</span>
                    </button>

                    <div x-show="open" x-collapse class="border-t">
                        @foreach ($g['items'] as $it)
                            @php $i = (int) $it['idx']; @endphp
                            <div class="px-4 py-3 grid grid-cols-12 gap-3 items-center hover:bg-gray-50/60">
                                <div class="col-span-4 min-w-0">
                                    <div class="text-sm font-medium truncate">
                                        {{ $it['title'] ?? 'Вариант' }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        SKU:
                                        <input type="text"
                                               class="fi-input block w-40 px-2 py-1 text-xs"
                                               wire:model.defer="data.variant_state.variants_editor.{{ $i }}.sku" />
                                    </div>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-xs text-gray-500 mb-1">Цена</label>
                                    <input type="number" step="1" min="0"
                                           class="fi-input block w-full px-2 py-1"
                                           wire:model.defer="data.variant_state.variants_editor.{{ $i }}.price" />
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-xs text-gray-500 mb-1">Остаток</label>
                                    <input type="number" step="1" min="0"
                                           class="fi-input block w-full px-2 py-1"
                                           wire:model.defer="data.variant_state.variants_editor.{{ $i }}.stock" />
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Доступно</label>
                                    <input type="checkbox"
                                           class="fi-input"
                                           wire:model.defer="data.variant_state.variants_editor.{{ $i }}.available" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>