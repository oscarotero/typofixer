<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\Ellipsis;

class EllipsisTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                'Hello...',
                'Hello…',
            ],
            [
                'Hello....',
                'Hello…',
            ],
            [
                'Hello..',
                'Hello…',
            ],
            [
                '<strong>Hello</strong>...',
                '<strong>Hello</strong>…',
            ],
            [
                '<strong>Hello...</strong>.',
                '<strong>Hello…</strong>',
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::fix($text, [
            new Ellipsis(),
        ]);

        $this->assertSame($expect, $result);
    }
}
