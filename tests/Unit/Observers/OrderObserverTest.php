<?php

namespace Tests\Unit\Observers;

use App\Observers\OrderObserver;
use Tests\TestCase;

/**
 * Class OrderObserverTest.
 *
 * @covers \App\Observers\OrderObserver
 */
final class OrderObserverTest extends TestCase
{
    private OrderObserver $orderObserver;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->orderObserver = new OrderObserver();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->orderObserver);
    }

    public function testCreated(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testUpdated(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testDeleted(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testRestored(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testForceDeleted(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
