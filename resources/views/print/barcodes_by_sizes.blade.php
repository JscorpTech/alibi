<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
@page { 
  size: 40mm 30mm; 
  margin: 0;
}

@page:last {
  size: 40mm 30mm;
  margin: 0;
}

* { 
  box-sizing: border-box; 
}

html, body { 
  margin: 0; 
  padding: 0; 
}

.label {
  width: 40mm;
  height: 30mm;
  padding: 1mm;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  page-break-inside: avoid;
  break-inside: avoid;
}

.label:last-child {
  page-break-after: avoid;
  break-after: avoid;
}

.title {
  font-size: 9px;
  font-weight: 600;
  line-height: 1.05;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.meta {
  font-size: 8px;
  line-height: 1.05;
  display: flex;
  justify-content: space-between;
  gap: 2px;
}

.price {
  font-size: 10px;
  font-weight: 700;
  margin-top: 0.5mm;
}

.code {
  margin-top: 0.5mm;
}

.code svg {
  display: block;
  width: 100%;
  height: 10mm;
}

.barcode-text {
  text-align: center;
  font-size: 7px;
}

.noprint {
  padding: 10px;
}

@media print {
  .noprint { 
    display: none !important; 
  }
}
</style>
</head>
<body>

<div class="noprint">
  <button onclick="window.print()">🖨️ Печать</button>
</div>

@foreach($items as $it)
  @for($i = 0; $i < ($it['repeat'] ?? 1); $i++)
    <div class="label">
      <div class="title">{{ $it['name'] }}</div>
      <div class="meta">
        <span>SKU: {{ $it['sku'] ?? '—' }}</span>
        <span>{{ $it['size'] ? 'Размер: '.$it['size'] : '' }}</span>
      </div>
      <div class="price">{{ number_format($it['price'] ?? 0, 0, '.', ' ') }} сум</div>
      <div class="code">
        {!! DNS1D::getBarcodeSVG($it['barcode'], 'EAN13', 0.9, 22) !!}
        <div class="barcode-text">{{ $it['barcode'] }}</div>
      </div>
    </div>
  @endfor
@endforeach

@php
  $totalLabels = collect($items)->sum(fn($it) => $it['repeat'] ?? 1);
@endphp

<script>
// Schitaem skolko etiketok
const totalLabels = {{ $totalLabels }};
console.log('Total labels:', totalLabels);
</script>

<script>
window.onload = () => window.print();
</script>

</body>
</html>