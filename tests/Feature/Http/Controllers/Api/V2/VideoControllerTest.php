<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V2\VideoController;
use Tests\TestCase;

/**
 * Class VideoControllerTest.
 *
 * @covers \App\Http\Controllers\Api\V2\VideoController
 */
final class VideoControllerTest extends TestCase
{
    private VideoController $videoController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->videoController = new VideoController();
        $this->app->instance(VideoController::class, $this->videoController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->videoController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
