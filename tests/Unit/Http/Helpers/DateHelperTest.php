<?php

namespace Tests\Unit\Http\Helpers;

use App\Http\Helpers\DateHelper;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class DateHelperTest.
 *
 * @covers \App\Http\Helpers\DateHelper
 */
final class DateHelperTest extends TestCase
{
    private DateHelper $dateHelper;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->dateHelper = new DateHelper();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->dateHelper);
    }

    public function testGetDate(): void
    {
        $this->assertMatchesRegularExpression('/(.*?) (.*?) (.*?):(.*?)*$/', $this->dateHelper::getDate(Carbon::now()));
    }
}
