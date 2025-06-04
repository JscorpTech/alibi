<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\DeleteAccount;
use Tests\TestCase;

/**
 * Class DeleteAccountTest.
 *
 * @covers \App\Http\Controllers\DeleteAccount
 */
final class DeleteAccountTest extends TestCase
{
    private DeleteAccount $deleteAccount;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->deleteAccount = new DeleteAccount();
        $this->app->instance(DeleteAccount::class, $this->deleteAccount);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deleteAccount);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
