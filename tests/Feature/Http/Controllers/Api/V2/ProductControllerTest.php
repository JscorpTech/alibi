<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V2\ProductController;
use Tests\TestCase;

/**
 * Class ProductControllerTest.
 *
 * @covers \App\Http\Controllers\Api\V2\ProductController
 */
final class ProductControllerTest extends TestCase
{
    private ProductController $productController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->productController = new ProductController();
        $this->app->instance(ProductController::class, $this->productController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }

    public function testView(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
