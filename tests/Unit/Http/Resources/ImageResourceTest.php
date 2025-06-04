<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class ImageResourceTest.
 *
 * @covers \App\Http\Resources\Api\MediaResource
 */
final class ImageResourceTest extends TestCase
{
    private MediaResource $imageResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->imageResource = new MediaResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->imageResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->imageResource->toArray($request));
    }
}
