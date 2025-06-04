<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Http\Requests\Api\BasketRequest;
use Tests\TestCase;

/**
 * Class BasketRequestTest.
 *
 * @covers \App\Http\Requests\Api\BasketRequest
 */
final class BasketRequestTest extends TestCase
{
    private BasketRequest $basketRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->basketRequest = new BasketRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->basketRequest);
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
