<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\GustController;
use Tests\TestCase;

/**
 * Class GustControllerTest.
 *
 * @covers \App\Http\Controllers\GustController
 */
final class GustControllerTest extends TestCase
{
    private GustController $gustController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->gustController = new GustController();
        $this->app->instance(GustController::class, $this->gustController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->gustController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
