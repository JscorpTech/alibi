<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Video;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class VideoTest.
 *
 * @covers \App\Http\Resources\Video
 */
final class VideoTest extends TestCase
{
    private Video $video;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->video = new Video();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->video);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->video->toArray($request));
    }
}
