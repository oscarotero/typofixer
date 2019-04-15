<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\CharsInside;

class CharsInsideTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<p><strong>Hello</strong>,</p>',
                '<p><strong>Hello,</strong></p>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::runFixers($text, new CharsInside());

        $this->assertSame($expect, $result);
    }
}
