<style>
[data-plus] .filepond--root {
  position: relative;
  height: var(--box-h, 120px);
}

/* Отключаем внутренние layout-эффекты FilePond */
[data-plus] .filepond--list-scroller,
[data-plus] .filepond--item-panel,
[data-plus] .filepond--item {
  position: static !important;
  inset: auto !important;
}

/* Растягиваем панель и делаем дроп-зону на весь контейнер */
[data-plus] .filepond--panel-root {
  height: 100% !important;
}

[data-plus] .filepond--drop-label {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 !important;
  margin: 0 !important;
  background: transparent !important;
  border: 0 !important;
  z-index: 2;
}

/* Обнуляем label и скрываем текст */
[data-plus] .filepond--drop-label label {
  position: relative;
  width: 100%;
  height: 100%;
  font-size: 0;
  cursor: pointer;
}

/* Сам плюс */
[data-plus] .filepond--drop-label label::before {
  content: '+';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 56px;
  height: 56px;
  border-radius: 12px;
  border: 1px dashed #475569;
  background: #f8fafc;
  color: #0f172a;
  font-size: 34px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .2s ease;
}

/* Hover эффект */
[data-plus] .filepond--drop-label:hover label::before {
  background: #e2e8f0;
  border-color: #334155;
}

/* 🌙 Тёмная тема */
:root.dark [data-plus] .filepond--drop-label label::before {
  background: #1e293b;
  border-color: #475569;
  color: #f8fafc;
}
:root.dark [data-plus] .filepond--drop-label:hover label::before {
  background: #334155;
  border-color: #94a3b8;
}
</style>