<?php

namespace Tests\Unit\Http\Resources\Api\Option;

use App\Http\Resources\Api\Option\ColorOptionResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class ColorOptionResourceTest.
 *
 * @covers \App\Http\Resources\Api\Option\ColorOptionResource
 */
final class ColorOptionResourceTest extends TestCase
{
    private ColorOptionResource $colorOptionResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->colorOptionResource = new ColorOptionResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->colorOptionResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->colorOptionResource->toArray($request));
    }
}
