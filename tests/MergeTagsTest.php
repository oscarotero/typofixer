<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
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
                '<strong><i>Hello</i> world</strong>',
            ],
            [
                '<p>hello</p> <p>world</p>',
                '<p>hello</p> <p>world</p>',
            ],
            [
                '<strong><a>hello</a></strong> <strong><a>world</a></strong> <strong><a>all</a></strong>',
                '<strong><a>hello</a> <a>world</a> <a>all</a></strong>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::runFixers($text, new MergeTags());

        $this->assertSame($expect, $result);
    }
}
