{{-- resources/views/print/barcodes.blade.php --}}
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  /* Строго 40×30 мм, БЕЗ внешних полей */
  @page { size: 40mm 30mm; margin: 0; }

  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; }

  /* Зафиксировать геометрию в режиме печати (важно для Safari) */
  @media print {
    html, body { width: 40mm; height: 30mm; overflow: hidden; }
  }

  /* Одна этикетка = вся страница, без разрывов */
  .grid { display: block; padding: 0; margin: 0; }
  .label{
    width: 40mm;               /* ровно по странице */
    height: 30mm;
    padding: 1mm;              /* внутренние поля */
    page-break-inside: avoid;
    break-inside: avoid;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* компактные шрифты */
  .title { font-size: 9px; font-weight: 600; line-height: 1.05; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .meta  { font-size: 8px; line-height: 1.05; display: flex; justify-content: space-between; gap: 2px; }
  .price { font-size: 10px; font-weight: 700; margin-top: 0.5mm; }

  .code  { margin-top: 0.5mm; page-break-inside: avoid; }
  .code svg { display:block; width: 100%; height: 10mm; } /* ещё компактнее */
  .barcode-text { text-align:center; font-size: 7px; }

  .noprint { padding: 6px; }
  @media print { .noprint { display:none } }
</style>
</head>
<body>
<div class="noprint">
  <button onclick="window.print()">🖨️ Печать</button>
</div>

<div class="grid">
@foreach($items as $it)
  @for($i = 0; $i < $qty; $i++)
    <div class="label">
      <div class="title">{{ $it['name'] }}</div>
      <div class="meta">
        <span>SKU: {{ $it['sku'] ?? '—' }}</span>
        <span>{{ $it['size'] ? 'Размер: '.$it['size'] : '' }}</span>
      </div>
      <div class="price">{{ number_format($it['price'], 0, '.', ' ') }} сум</div>
     <div class="code">
  {!! DNS1D::getBarcodeSVG($it['barcode'], 'EAN13', 0.9, 22) !!}
  {{-- можно убрать строчку ниже, если цифры дублируются --}}

</div>
    </div>
  @endfor
@endforeach
</div>

<script>window.onload = () => window.print();</script>
</body>
</html>