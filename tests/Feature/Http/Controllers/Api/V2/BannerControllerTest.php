<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V2\BannerController;
use Tests\TestCase;

/**
 * Class BannerControllerTest.
 *
 * @covers \App\Http\Controllers\Api\V2\BannerController
 */
final class BannerControllerTest extends TestCase
{
    private BannerController $bannerController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->bannerController = new BannerController();
        $this->app->instance(BannerController::class, $this->bannerController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->bannerController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
