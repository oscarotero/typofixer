<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\AddSpaceAfter;

class SpaceAfterTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<p>Hello,world</p>',
                '<p>Hello, world</p>',
            ],
            [
                '<p>Hello,world,hello</p>',
                '<p>Hello, world, hello</p>',
            ],
            [
                '<p>Hello!world</p>',
                '<p>Hello! world</p>',
            ],
            [
                '<p>Hello!!world</p>',
                '<p>Hello!! world</p>',
            ],
            [
                '<p>Hello.world</p>',
                '<p>Hello. world</p>',
            ],
            [
                '<p>1.2</p>',
                '<p>1.2</p>',
            ],
            [
                '<p>1,2</p>',
                '<p>1,2</p>',
            ],
            [
                '<p><strong>hello,</strong>world</p>',
                '<p><strong>hello,</strong> world</p>',
            ],
            [
                '<p>hello...world</p>',
                '<p>hello... world</p>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::fix($text, [
            new AddSpaceAfter(),
        ]);

        $this->assertSame($expect, $result);
    }
}
