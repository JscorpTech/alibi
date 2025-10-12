@if(is_array($get('variant_preview')) && count($get('variant_preview')))
  <div class="rounded-lg border p-3">
    <div class="text-sm font-semibold mb-2">Комбинации к созданию:</div>
    <div class="grid gap-1">
      @foreach($get('variant_preview') as $row)
        <div class="text-sm text-gray-700">
          {{ collect($row)->map(fn($v,$k)=>"$k: $v")->implode(' • ') }}
        </div>
      @endforeach
    </div>
    <div class="text-xs text-gray-500 mt-2">
      Нажмите «Создать варианты», чтобы записать отсутствующие комбинации.
    </div>
  </div>
@endif