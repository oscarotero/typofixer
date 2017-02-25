<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\RemoveInnerTags;

class RemoveInnerTagsTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<strong>Hello <strong>world</strong></strong>',
                '<strong>Hello world</strong>',
            ],
            [
                '<strong>Hello <i><strong>world</strong></i></strong>',
                '<strong>Hello <i>world</i></strong>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::fix($text, [
            new RemoveInnerTags(),
        ]);

        $this->assertSame($expect, $result);
    }
}
