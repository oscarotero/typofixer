<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Fixer;
use Typofixer\Fixers\MergeTags;

class MergeTagsTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<strong>Hello</strong><strong> world</strong>',
                '<strong>Hello world</strong>',
            ],
            [
                '<strong>Hello</strong> <strong>world</strong>',
                '<strong>Hello world</strong>',
            ],
            [
                '<strong><i>Hello</i></strong> <strong>world</strong>',
                '<strong><i>Hello </i>world</strong>',
            ],
            [
                '<p>hello</p> <p>world</p>',
                '<p>hello</p> <p>world</p>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Fixer::fix($text, [
            new MergeTags(),
        ]);

        $this->assertSame($expect, $result);
    }
}
