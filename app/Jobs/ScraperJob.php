<?php

namespace App\Jobs;

use App\Services\ScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;

class ScraperJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        //
    }

    /**
     * Ox system scraper
     *
     */
    #[NoReturn]
    public function handle(): void
    {
        try {
            $service = new ScraperService();
            $service->getProducts();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
