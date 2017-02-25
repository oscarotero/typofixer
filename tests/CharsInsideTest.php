<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Fixer;
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
        $result = Fixer::fix($text, [
            new CharsInside(),
        ]);

        $this->assertSame($expect, $result);
    }
}
