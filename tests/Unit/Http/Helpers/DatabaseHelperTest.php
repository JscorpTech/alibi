<?php

namespace Tests\Unit\Http\Helpers;

use App\Http\Helpers\DatabaseHelper;
use Tests\TestCase;

/**
 * Class DatabaseHelperTest.
 *
 * @covers \App\Http\Helpers\DatabaseHelper
 */
final class DatabaseHelperTest extends TestCase
{
    private DatabaseHelper $databaseHelper;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->databaseHelper = new DatabaseHelper();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->databaseHelper);
    }

    public function testGetRandomId(): void
    {
        $this->assertIsNumeric($this->databaseHelper::getRandomId('users'));
    }
}
