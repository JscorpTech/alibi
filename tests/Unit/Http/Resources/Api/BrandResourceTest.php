<?php

namespace Tests\Unit\Http\Resources\Api;

use App\Http\Resources\Api\BrandResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class BrandResourceTest.
 *
 * @covers \App\Http\Resources\Api\BrandResource
 */
final class BrandResourceTest extends TestCase
{
    private BrandResource $brandResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->brandResource = new BrandResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->brandResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->brandResource->toArray($request));
    }
}
