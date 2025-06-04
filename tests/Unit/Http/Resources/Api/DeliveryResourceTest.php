<?php

namespace Tests\Unit\Http\Resources\Api;

use App\Http\Resources\Api\DeliveryResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class DeliveryResourceTest.
 *
 * @covers \App\Http\Resources\Api\DeliveryResource
 */
final class DeliveryResourceTest extends TestCase
{
    private DeliveryResource $deliveryResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->deliveryResource = new DeliveryResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deliveryResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->deliveryResource->toArray($request));
    }
}
