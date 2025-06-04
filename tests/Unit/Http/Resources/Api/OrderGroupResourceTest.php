<?php

namespace Tests\Unit\Http\Resources\Api;

use App\Http\Resources\Api\OrderGroupResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class OrderGroupResourceTest.
 *
 * @covers \App\Http\Resources\Api\OrderGroupResource
 */
final class OrderGroupResourceTest extends TestCase
{
    private OrderGroupResource $orderGroupResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->orderGroupResource = new OrderGroupResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->orderGroupResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->orderGroupResource->toArray($request));
    }
}
