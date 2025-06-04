<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BrandController;
use Tests\TestCase;

/**
 * Class BrandControllerTest.
 *
 * @covers \App\Http\Controllers\Api\V1\BrandController
 */
final class BrandControllerTest extends TestCase
{
    private BrandController $brandController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->brandController = new BrandController();
        $this->app->instance(BrandController::class, $this->brandController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->brandController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
