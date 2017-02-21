<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Fixer;
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
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Fixer::fix($text, [
            new SpaceTags(),
        ]);

        $this->assertSame($expect, $result);
    }
}
