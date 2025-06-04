<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\AddressController;
use Tests\TestCase;

/**
 * Class AddressControllerTest.
 *
 * @covers \App\Http\Controllers\Api\V1\AddressController
 */
final class AddressControllerTest extends TestCase
{
    private AddressController $addressController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->addressController = new AddressController();
        $this->app->instance(AddressController::class, $this->addressController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->addressController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
