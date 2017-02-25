<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\RemoveEmptyTags;

class RemoveEmptyTagsTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<strong></strong> Hello world',
                'Hello world',
            ],[
                '<strong></strong> Hello world <strong></strong>',
                'Hello world',
            ],[
                'Hello<strong> </strong>world',
                'Hello world',
            ],[
                'Hello<strong><i></i></strong> world',
                'Hello world',
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::fix($text, [
            new RemoveEmptyTags(),
        ]);

        $this->assertSame($expect, $result);
    }
}
