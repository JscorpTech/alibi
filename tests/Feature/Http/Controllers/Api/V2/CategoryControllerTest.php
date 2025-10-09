<?php

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V2\CategoryController;
use Tests\TestCase;

/**
 * Class CategoryControllerTest.
 *
 * @covers \App\Http\Controllers\Api\V2\CategoryController
 */
final class CategoryControllerTest extends TestCase
{
    private CategoryController $categoryController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->categoryController = new CategoryController();
        $this->app->instance(CategoryController::class, $this->categoryController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->categoryController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
