import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        // оставь, если у тебя действительно есть эти файлы:
        // 'resources/css/app.css',
        // 'resources/js/app.js',

        // 👇 ОБЯЗАТЕЛЬНО: твоя тема для Filament
        'resources/css/filament/admin/theme.css',
      ],
      refresh: [
        ...refreshPaths,
        'app/Filament/**',
        'app/Forms/Components/**',
        'app/Livewire/**',
        'app/Infolists/Components/**',
        'app/Providers/Filament/**',
        'app/Tables/Columns/**',
        'resources/views/filament/**',
      ],
    }),
  ],
})