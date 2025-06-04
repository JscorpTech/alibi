<?php

namespace Tests\Unit\Http\Requests\Filters;

use App\Http\Requests\Filters\Product;
use Tests\TestCase;

/**
 * Class ProductTest.
 *
 * @covers \App\Http\Requests\Filters\Product
 */
final class ProductTest extends TestCase
{
    private Product $product;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->product = new Product();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->product);
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
