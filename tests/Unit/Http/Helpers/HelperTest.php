<?php

namespace Tests\Unit\Http\Helpers;

use App\Http\Helpers\Helper;
use Tests\TestCase;

/**
 * Class HelperTest.
 *
 * @covers \App\Http\Helpers\Helper
 */
final class HelperTest extends TestCase
{
    private Helper $helper;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->helper = new Helper();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->helper);
    }

    public function testRemoveNullData(): void
    {
        $this->assertEquals(['salom'], $this->helper::removeNullData(['salom', null]));
    }

    public function testClearPhone(): void
    {
        $this->assertEquals('998943990509', $this->helper::clearPhone('998 (94) 399-05-09'));
    }

    public function testClearSpace(): void
    {
        $this->assertEquals('salomqalaysan', $this->helper::clearSpace('salom qalaysan'));
    }

    public function testCheckPhone(): void
    {
        $this->assertTrue($this->helper::checkPhone('998943990509'));
        $this->assertFalse($this->helper::checkPhone('admin@gmail.com'));
    }
}
