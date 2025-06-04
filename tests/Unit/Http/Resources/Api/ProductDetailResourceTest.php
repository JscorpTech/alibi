<?php

namespace Tests\Unit\Http\Resources\Api;

use App\Http\Resources\Api\Product\ProductDetailResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class ProductDetailResourceTest.
 *
 * @covers \App\Http\Resources\Api\Product\ProductDetailResource
 */
final class ProductDetailResourceTest extends TestCase
{
    private ProductDetailResource $productDetailResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->productDetailResource = new ProductDetailResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productDetailResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->productDetailResource->toArray($request));
    }
}
