<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ScraperJob;
use Tests\TestCase;

/**
 * Class ScraperJobTest.
 *
 * @covers \App\Jobs\ScraperJob
 */
final class ScraperJobTest extends TestCase
{
    private ScraperJob $scraperJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->scraperJob = new ScraperJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->scraperJob);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->scraperJob->handle();
    }
}
