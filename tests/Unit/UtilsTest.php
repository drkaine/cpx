<?php

namespace Cpx\Tests\Unit;

use Cpx\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testArrayMapAssoc(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];
        
        $result = Utils::arrayMapAssoc(function($key, $value) {
            return [$key => $value * 2];
        }, $input);

        $this->assertEquals(['a' => 2, 'b' => 4, 'c' => 6], $result);
    }
}