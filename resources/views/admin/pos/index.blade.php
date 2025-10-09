{{-- resources/views/admin/pos/index.blade.php --}}
<div style="padding:20px;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial">
  <h1 style="font-size:22px;margin-bottom:12px">Касса (POS) — черновик</h1>
  <p style="color:#666">Страница подключена. Далее добавим интерфейс и логику.</p>

  <form method="POST" action="{{ route('admin.pos.scan') }}" style="margin-top:16px">
    @csrf
    <input type="text" name="q" placeholder="Проверка: введите текст и нажмите Добавить"
           style="padding:8px;border:1px solid #ddd;border-radius:6px;width:60%">
    <button style="padding:8px 12px;border-radius:6px;border:0;background:#111;color:#fff">Добавить</button>
  </form>
</div>