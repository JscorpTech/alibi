<?php

namespace Tests\Unit\Jobs;

use App\Jobs\NotificationJob;
use Tests\TestCase;

/**
 * Class NotificationJobTest.
 *
 * @covers \App\Jobs\NotificationJob
 */
final class NotificationJobTest extends TestCase
{
    private NotificationJob $notificationJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationJob = new NotificationJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->notificationJob);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->notificationJob->handle();
    }
}
