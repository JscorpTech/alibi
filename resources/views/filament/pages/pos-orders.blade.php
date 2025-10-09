<x-filament-panels::page>
    <div class="space-y-6">
        @foreach ($this->orders as $order)
            <x-filament::section class="p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-sm">Чек {{ $order['number'] }}</h3>
                        <p class="text-xs text-gray-500">
                            {{ $order['created_at'] }} • {{ strtoupper($order['payment_method']) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-medium">{{ $order['total'] }} сум</span>
                    </div>
                </div>

                <div class="mt-3 space-y-1 text-xs text-gray-700">
                    @foreach ($order['items'] as $it)
                        <div class="flex justify-between">
                            <span>
                                {{ $it['name'] }}
                                @if($it['size'] || $it['color'])
                                    <span class="text-gray-400">
                                        ({{ $it['size'] ?? '' }}{{ $it['size'] && $it['color'] ? ' • ' : '' }}{{ $it['color'] ?? '' }})
                                    </span>
                                @endif
                            </span>
                            <span>{{ $it['qty'] }} × {{ $it['price'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>