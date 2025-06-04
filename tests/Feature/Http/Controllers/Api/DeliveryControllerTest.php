<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\V1\DeliveryController;
use Tests\TestCase;

/**
 * Class DeliveryControllerTest.
 *
 * @covers \App\Http\Controllers\Api\V1\DeliveryController
 */
final class DeliveryControllerTest extends TestCase
{
    private DeliveryController $deliveryController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->deliveryController = new DeliveryController();
        $this->app->instance(DeliveryController::class, $this->deliveryController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deliveryController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
