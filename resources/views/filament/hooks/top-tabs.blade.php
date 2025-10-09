{{-- resources/views/filament/hooks/top-tabs.blade.php --}}
@php
  $tabs = [
    ['label' => 'Products', 'url' => \App\Filament\Resources\ProductResource::getUrl('index')],
    ['label' => 'POS',      'url' => route('filament.admin.pages.pos')],
  ];
  $current = url()->current();
@endphp

<div class="hidden md:flex items-center gap-2 ml-6">
  @foreach ($tabs as $t)
    @php $active = str_starts_with($current, $t['url']); @endphp
    <a href="{{ $t['url'] }}"
       class="px-3 py-2 rounded-xl text-sm transition
              {{ $active ? 'bg-pink-500 text-white shadow' : 'text-white/80 hover:bg-white/10' }}">
      {{ $t['label'] }}
    </a>
  @endforeach
</div>