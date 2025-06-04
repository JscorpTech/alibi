<?php

namespace Tests\Unit\Http\Resources\Api;

use App\Http\Resources\Api\SubCategoryResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class SubCategoryResourceTest.
 *
 * @covers \App\Http\Resources\Api\SubCategoryResource
 */
final class SubCategoryResourceTest extends TestCase
{
    private SubCategoryResource $subCategoryResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->subCategoryResource = new SubCategoryResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->subCategoryResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->subCategoryResource->toArray($request));
    }
}
