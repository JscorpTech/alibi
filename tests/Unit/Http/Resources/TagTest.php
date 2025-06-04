<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Tag;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class TagTest.
 *
 * @covers \App\Http\Resources\Tag
 */
final class TagTest extends TestCase
{
    private Tag $tag;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->tag = new Tag();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->tag);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        self::assertSame([], $this->tag->toArray($request));
    }
}
