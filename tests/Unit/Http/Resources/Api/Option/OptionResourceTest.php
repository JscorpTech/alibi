<?php

namespace Tests\Unit\Http\Resources\Api\Option;

use App\Http\Resources\Api\Option\OptionResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class OptionResourceTest.
 *
 * @covers \App\Http\Resources\Api\Option\OptionResource
 */
final class OptionResourceTest extends TestCase
{
    private OptionResource $optionResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->optionResource = new OptionResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->optionResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->optionResource->toArray($request));
    }
}
