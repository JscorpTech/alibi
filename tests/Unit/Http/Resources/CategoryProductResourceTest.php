<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\CategoryProductResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class CategoryProductResourceTest.
 *
 * @covers \App\Http\Resources\CategoryProductResource
 */
final class CategoryProductResourceTest extends TestCase
{
    private CategoryProductResource $categoryProductResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->categoryProductResource = new CategoryProductResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->categoryProductResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->categoryProductResource->toArray($request));
    }
}
