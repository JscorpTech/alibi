<div class="rounded-xl border border-gray-200 bg-white p-4" x-data="{
      maxAxes: 2,
      allowed: ['Size','Color'],
      get axes() {
          // безопасно читаем состояние из Livewire/Filament
          const cur = $wire.$get('data.variant_state.variant_options');
          return Array.isArray(cur) ? cur : [];
      },
      canAdd() {
          // нельзя, если уже добавили все возможные оси
          if (this.axes.length >= this.maxAxes) return false;
          // есть ли ещё свободные названия осей
          return this.allowed.some(n => !this.axes.find(a => a?.name === n));
      },
      nextAxisName() {
          // вернём первое доступное название (например, сначала Size, потом Color)
          for (const n of this.allowed) {
              if (!this.axes.find(a => a?.name === n)) return n;
          }
          return null;
      },
      async addAxis() {
          if (!this.canAdd()) {
              // необязательно: мягкое уведомление
              window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'info', text: 'Больше параметров добавить нельзя' }}));
              return;
          }
          const name = this.nextAxisName();
          const next = [...this.axes, { name, values: [] }];
          this.adding = true;
          try {
              await $wire.$set('data.variant_state.variant_options', next);
              // опционально — дать фокус свежему селекту (если он в DOM с data-axis-select)
              requestAnimationFrame(() => {
                  const last = document.querySelector('[data-axis-select]:last-of-type');
                  last?.focus();
              });
          } finally {
              this.adding = false;
          }
      },
      adding: false,
  }">
    <div class="flex items-center justify-between gap-3">
        <button type="button" class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-medium
             hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500
             disabled:opacity-60 disabled:cursor-not-allowed" :aria-disabled="!canAdd()"
            :disabled="adding || !canAdd()" @click="addAxis()">
            <span class="text-lg leading-none" aria-hidden="true">＋</span>
            <span x-text="canAdd() ? 'Добавить параметр (размер/цвет)' : 'Больше добавить нельзя'"></span>

            <!-- мини-спиннер -->
            <svg x-show="adding" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"
                aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
        </button>

        <!-- счётчик/подсказка -->
        <div class="text-xs text-gray-500">
            <span x-text="`Выбрано: ${axes.length} / ${maxAxes}`"></span>
        </div>
    </div>

    <!-- необязательная подсказка о приоритете -->
    <p class="mt-2 text-xs text-gray-500">
        Советуем сначала добавить <b>Size</b>, затем <b>Color</b>. Дубли осей автоматически блокируются.
    </p>
</div>