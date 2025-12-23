<?php

namespace App\Jobs;

use App\Models\Video;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessVideoJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(): void
{
    try {
        logger('▶️ Обработка началась для видео ID: ' . $this->video->id);

        $originalPath = storage_path('app/' . $this->video->path);
        logger('🟡 Исходный путь: ' . $originalPath);

        $randomId = uniqid();
        $convertedName = "converted_$randomId.mp4";
        $thumbnailName = "thumbnail_$randomId.jpg";

        $convertedPath = public_path('videos/' . $convertedName);
        $thumbnailPath = public_path('thumbnails/' . $thumbnailName);

        // Убедись, что директории существуют
        if (!file_exists(dirname($convertedPath))) {
            mkdir(dirname($convertedPath), 0755, true);
        }

        if (!file_exists(dirname($thumbnailPath))) {
            mkdir(dirname($thumbnailPath), 0755, true);
        }

        // FFmpeg: конвертация
        $commandConvert = "ffmpeg -i \"$originalPath\" -vcodec libx264 -acodec aac \"$convertedPath\"";
        exec($commandConvert);

        // FFmpeg: превью
        $commandThumbnail = "ffmpeg -i \"$originalPath\" -ss 00:00:00.100 -vframes 1 \"$thumbnailPath\"";
        exec($commandThumbnail . ' 2>&1', $output, $code);
        logger('📸 Output FFmpeg thumbnail:', $output);

        // Обновление записи
        $this->video->update([
            'converted_path' => 'videos/' . $convertedName,     // важно: без "public/"
            'thumbnail_path' => 'thumbnails/' . $thumbnailName,
            'status' => 'ready',
        ]);

        logger('✅ Обработка завершена успешно для видео ID: ' . $this->video->id);
    } catch (\Exception $e) {
        logger('❌ Ошибка при обработке видео ID: ' . $this->video->id);
        logger($e->getMessage());
    }
}
}