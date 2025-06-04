<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\VideoLikeRequest;
use Tests\TestCase;

/**
 * Class VideoLikeRequestTest.
 *
 * @covers \App\Http\Requests\VideoLikeRequest
 */
final class VideoLikeRequestTest extends TestCase
{
    private VideoLikeRequest $videoLikeRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->videoLikeRequest = new VideoLikeRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->videoLikeRequest);
    }

    public function testAuthorize(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testRules(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
