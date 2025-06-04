<?php

namespace Tests\Unit\Http\Resources\Api;

use App\Http\Resources\Api\BasketResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class BasketResourceTest.
 *
 * @covers \App\Http\Resources\Api\BasketResource
 */
final class BasketResourceTest extends TestCase
{
    private BasketResource $basketResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->basketResource = new BasketResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->basketResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->basketResource->toArray($request));
    }
}
