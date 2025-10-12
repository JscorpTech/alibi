<div
  class="rounded-xl border border-gray-200 bg-white p-4 space-y-4"
  x-data="shopifyOptions()"
  x-init="init()"
>
  {{-- Шапка --}}
  <div class="flex items-center justify-between">
    <div>
      <h3 class="text-sm font-semibold">Опции товара</h3>
      <p class="text-xs text-gray-500">Добавь опции (Size/Color). Значения вводи и жми Enter / запятую / Tab.</p>
    </div>

    {{-- Кнопка «Добавить опцию» --}}
    <div class="relative" x-data="{open:false}">
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50 disabled:opacity-50"
        :disabled="availableNames().length === 0"
        @click="open = !open"
      >
        <svg width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M8 1.5a.75.75 0 01.75.75V7.25H14a.75.75 0 010 1.5H8.75V14a.75.75 0 01-1.5 0V8.75H2a.75.75 0 010-1.5h5.25V2.25A.75.75 0 018 1.5z"/></svg>
        Добавить опцию
      </button>

      <div
        x-show="open"
        x-cloak
        @click.away="open=false"
        class="absolute right-0 mt-2 w-40 rounded-md border bg-white shadow-md overflow-hidden"
      >
        <template x-if="availableNames().length === 0">
          <div class="px-3 py-2 text-sm text-gray-500">Нет доступных</div>
        </template>
        <template x-for="n in availableNames()" :key="n">
          <button
            type="button"
            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50"
            @click="addAxis(n); open=false"
            x-text="n"
          ></button>
        </template>
      </div>
    </div>
  </div>

  {{-- Список опций --}}
  <template x-for="(ax, i) in axes" :key="i">
    <div class="rounded-lg border p-3 space-y-2">
      <div class="flex items-center justify-between gap-3">
        {{-- Select имени --}}
        <div class="flex items-center gap-2">
          <label class="text-xs text-gray-500">Опция</label>
          <select
            class="rounded-md border px-2 py-1 text-sm"
            x-model="axes[i].name"
            @change="normalizeAxis(i)"
          >
            <template x-for="n in allNames" :key="n">
              <option :value="n" x-text="n" :disabled="isNameTaken(n, i)"></option>
            </template>
          </select>
        </div>

        <button type="button" class="text-xs text-red-600 hover:underline" @click="removeAxis(i)">
          Удалить
        </button>
      </div>

      {{-- Токены значений + инпут --}}
      <div class="mt-1 flex flex-wrap gap-2 rounded-md border px-2 py-2" @click="$refs['val'+i]?.focus()">
        <template x-if="axes[i].values.length === 0">
          <span class="text-xs text-gray-400">например: 41, 42, 43</span>
        </template>

        <template x-for="(v, vi) in axes[i].values" :key="v">
          <span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs">
            <span x-text="v"></span>
            <button type="button" class="opacity-60 hover:opacity-100" @click="removeValue(i, v)">×</button>
          </span>
        </template>

        <input
          class="flex-1 min-w-[8rem] outline-none text-sm"
          :ref="'val'+i"
          type="text"
          placeholder="Введите и Enter / , / Tab"
          @keydown.enter.prevent="commitInput(i, $event.target)"
          @keydown.tab.prevent="commitInput(i, $event.target)"
          @keydown.comma.prevent="commitInput(i, $event.target)"
          @blur="commitInput(i, $event.target, true)"
        />
      </div>
    </div>
  </template>

  {{-- Кнопки снизу, включая «Готово» как в Shopify --}}
  <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
    <div class="text-xs text-gray-500">
      Оси: <b x-text="axes.length"></b> / 2
    </div>

    <div class="flex flex-wrap gap-2">
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm font-medium hover:bg-gray-50"
        @click="addAxis()"
        :disabled="availableNames().length===0"
      >
        <x-heroicon-o-plus class="h-4 w-4"/>
        Добавить ось
      </button>

      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg bg-gray-950 px-3 py-1.5 text-sm font-medium text-white hover:opacity-90"
        @click="buildAndPushVariants()"
      >
        <x-heroicon-o-check class="h-4 w-4"/>
        Готово — создать варианты
      </button>
    </div>
  </div>

  {{-- На всякий: скрытый input, чтобы не ругался бэкенд --}}
  <input type="hidden" name="variant_state[variant_options]" :value="json(axes)">
</div>

<script>
function shopifyOptions() {
  return {
    allNames: ['Size','Color'],
    axes: [],

    // ---------- init / wire ----------
    init() {
      const cur = this.getWire('data.variant_state.variant_options');
      this.axes = Array.isArray(cur)
        ? cur.map(a => ({ name: this.canon(a?.name), values: this.cleanVals(a?.values) }))
        : [];
      this.pushAxes(); // синхронизируем на запуске
    },
    getWire(key){ try{ return $wire?.$get?.(key); }catch{ return null; } },
    setWire(key,val){ try{ return $wire?.$set?.(key,val); }catch{ return null; } },

    // ---------- utils ----------
    canon(n){ const m={size:'Size',colour:'Color',color:'Color',Colour:'Color'}; const k=String(n||'').trim(); return m[k] ?? (this.allNames.includes(k)?k:'Size'); },
    cleanVals(vals){ return Array.isArray(vals)?[...new Set(vals.map(v=>String(v).trim()).filter(Boolean))]:[]; },
    availableNames(){ const used=new Set(this.axes.map(a=>a.name)); return this.allNames.filter(n=>!used.has(n)); },
    isNameTaken(name, idx){ return this.axes.some((a,i)=>i!==idx && a.name===name); },

    // ---------- axes CRUD ----------
    addAxis(name=null){
      const n = name ?? this.availableNames()[0] ?? null;
      if(!n) return;
      if(this.axes.find(a=>a.name===n)) return;
      this.axes.push({ name:n, values:[] });
      this.pushAxes();
    },
    removeAxis(i){ this.axes.splice(i,1); this.pushAxes(); },
    normalizeAxis(i){
      const a=this.axes[i];
      a.name=this.canon(a.name);
      if(this.isNameTaken(a.name,i)){
        const free=this.availableNames()[0];
        if(free) a.name=free;
      }
      this.pushAxes();
    },

    // ---------- values ----------
    commitInput(i, el, onBlur=false){
      const raw = String(el.value||'').trim();
      if(!raw && onBlur){ this.pushAxes(); return; }
      if(!raw) return;
      // поддержка "41, 42, 43"
      raw.split(',').map(s=>s.trim()).filter(Boolean).forEach(v=>this.addValue(i,v));
      el.value='';
      this.pushAxes();
    },
    addValue(i, v){
      v = String(v).trim();
      if(!v) return;
      const arr=this.axes[i].values;
      if(!arr.includes(v)) arr.push(v);
    },
    removeValue(i, v){
      const arr=this.axes[i].values;
      const j=arr.indexOf(v);
      if(j!==-1) arr.splice(j,1);
      this.pushAxes();
    },

    // ---------- push axes only ----------
    pushAxes(){
      const clean = this.axes.map(a => ({ name:a.name, values:[...a.values].sort((x,y)=>String(x).localeCompare(String(y),'ru')) }));
      this.setWire('data.variant_state.variant_options', clean);
    },

    // ---------- build variants like "Готово" ----------
    buildAndPushVariants(){
      // axes -> cartesian
      const axes = this.axes.filter(a => a?.name && Array.isArray(a.values) && a.values.length);
      if(!axes.length){
        this.setWire('data.variant_state.variants_draft', []);
        this.setWire('data.variant_state.variants_editor', []);
        this.setWire('data.variant_state.stocks', {});
        return;
      }

      let combos = [[]];
      for(const ax of axes){
        const next=[];
        for(const c of combos){
          for(const val of ax.values){
            next.push(Object.assign({}, c, { [ax.name]: val }));
          }
        }
        combos = next;
      }

      // сохранить старые количества, чтобы не потерять ввод
      const oldStocks = this.getWire('data.variant_state.stocks') || {};

      // key helper: id:ID (если есть) иначе attrs:HASH
      const rowKey = (row)=>{
        if(row?.id){ return 'id:'+parseInt(row.id); }
        const a = Object.assign({}, row?.attrs||{});
        const k = Object.keys(a).sort().reduce((o,kk)=> (o[kk]=a[kk], o), {});
        return 'attrs:'+ md5(JSON.stringify(k));
      };

      // md5 мини
      function md5(str){ return (window.CryptoJS?.MD5 ? String(window.CryptoJS.MD5(str)) : simpleHash(str)); }
      function simpleHash(s){ // заменитель, если CryptoJS не подключен
        let h=0; for(let i=0;i<s.length;i++){ h=(h<<5)-h+s.charCodeAt(i); h|=0; }
        // сделаем «псевдо md5» из 12 символов
        return ('00000000'+(h>>>0).toString(16)).slice(-8)+('0000'+(s.length%65535).toString(16)).slice(-4);
      }

      // собрать строки и новую карту stocks
      const rows=[];
      const stocks={};
      for(const attrsRaw of combos){
        const attrs = Object.keys(attrsRaw).sort().reduce((o,k)=> (o[k]=attrsRaw[k], o), {});
        const title = Object.entries(attrs).map(([k,v])=>`${k}: ${v}`).join(' / ');
        const row = { title, attrs, price:0, stock:0, available:true, sku:null };
        rows.push(row);

        const key = rowKey(row);
        stocks[key] = (oldStocks[key] ?? 0)|0;
      }

      // записать в Livewire/Filament
      this.setWire('data.variant_state.variants_draft', rows);
      this.setWire('data.variant_state.variants_editor', rows);
      this.setWire('data.variant_state.stocks', stocks);

      // опционально: мягкий всплывающий toast через Livewire event
      try { window.dispatchEvent(new CustomEvent('notify', { detail:{ type:'success', text:`Варианты созданы: ${rows.length}` }})); } catch {}
    },

    // вспомогательное
    json(v){ try{ return JSON.stringify(v); }catch{ return '[]'; } },
  };
}
</script>