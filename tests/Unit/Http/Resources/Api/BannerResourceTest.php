<?php

namespace Tests\Unit\Http\Resources\Api;

use App\Http\Resources\Api\BannerResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class BannerResourceTest.
 *
 * @covers \App\Http\Resources\Api\BannerResource
 */
final class BannerResourceTest extends TestCase
{
    private BannerResource $bannerResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->bannerResource = new BannerResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->bannerResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->bannerResource->toArray($request));
    }
}
