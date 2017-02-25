<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\SpaceTags;

class SpaceTagsTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<p><strong>Hello </strong> world</p>',
                '<p><strong>Hello</strong> world</p>',
            ],
            [
                '<p><strong>Hello </strong>world</p>',
                '<p><strong>Hello</strong> world</p>',
            ],
            [
                '<p><strong>Hello</strong> <i>world</i></p>',
                '<p><strong>Hello</strong> <i>world</i></p>',
            ],
            [
                '<p><strong>Hello </strong><i>world</i></p>',
                '<p><strong>Hello</strong> <i>world</i></p>',
            ],
            [
                '<p>Hello<strong> world </strong></p>',
                '<p>Hello <strong>world</strong></p>',
            ],
            [
                '<p><strong><i>Hello</i> </strong>world</p>',
                '<p><strong><i>Hello</i></strong> world</p>',
            ],
            [
                '<strong><a>hello</a></strong> <strong><a>world</a></strong>',
                '<strong><a>hello</a></strong> <strong><a>world</a></strong>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::fix($text, [
            new SpaceTags(),
        ]);

        $this->assertSame($expect, $result);
    }
}
