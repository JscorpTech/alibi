<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessVideoJob;
use Tests\TestCase;

/**
 * Class ProcessVideoJobTest.
 *
 * @covers \App\Jobs\ProcessVideoJob
 */
final class ProcessVideoJobTest extends TestCase
{
    private ProcessVideoJob $processVideoJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processVideoJob = new ProcessVideoJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->processVideoJob);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->processVideoJob->handle();
    }
}
