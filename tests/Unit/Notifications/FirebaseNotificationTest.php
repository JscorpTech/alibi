<?php

namespace Tests\Unit\Notifications;

use App\Notifications\FirebaseNotification;
use Tests\TestCase;

/**
 * Class FirebaseNotificationTest.
 *
 * @covers \App\Notifications\FirebaseNotification
 */
final class FirebaseNotificationTest extends TestCase
{
    private FirebaseNotification $firebaseNotification;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->firebaseNotification = new FirebaseNotification();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->firebaseNotification);
    }

    public function testVia(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testToMail(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testToArray(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
