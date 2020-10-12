<?php

namespace App\Tests\Utils;

use App\Utils\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{

    public function testRandom()
    {
        $this->assertSame(6, mb_strlen(Str::random(6)));
        $this->assertSame(10, mb_strlen(Str::random(10)));
        $this->assertSame(16, mb_strlen(Str::random()));
    }
}
