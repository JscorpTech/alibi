<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Http\Requests\Api\IsAlreadyRequest;
use Tests\TestCase;

/**
 * Class IsAlreadyRequestTest.
 *
 * @covers \App\Http\Requests\Api\IsAlreadyRequest
 */
final class IsAlreadyRequestTest extends TestCase
{
    private IsAlreadyRequest $isAlreadyRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->isAlreadyRequest = new IsAlreadyRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->isAlreadyRequest);
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
