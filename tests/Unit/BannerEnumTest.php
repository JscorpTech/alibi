<?php

namespace Tests\Unit;

use App\Enums\BannerEnum;
use App\Enums\BaseEnum;
use PHPUnit\Framework\TestCase;

class BannerEnumTest extends TestCase
{
    public function test_index(): void
    {
        $this->assertEquals('top', BannerEnum::TOP);
        $this->assertEquals('bottom', BannerEnum::BOTTOM);
        $this->assertEquals('top,bottom', BannerEnum::toString());
        $this->assertEquals(['top','bottom'], BannerEnum::toArray());
    }
}
