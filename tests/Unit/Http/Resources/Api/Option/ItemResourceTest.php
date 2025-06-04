<?php

namespace Tests\Unit\Http\Resources\Api\Option;

use App\Http\Resources\Api\Option\ItemResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class ItemResourceTest.
 *
 * @covers \App\Http\Resources\Api\Option\ItemResource
 */
final class ItemResourceTest extends TestCase
{
    private ItemResource $itemResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->itemResource = new ItemResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->itemResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->itemResource->toArray($request));
    }
}
