<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\RemoveSpaceAfter;

class RemoveAfterBeforeTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<p>Hello ¡ world!</p>',
                '<p>Hello ¡world!</p>',
            ],
            [
                '<p>Hello ¿ all? ¿ world?</p>',
                '<p>Hello ¿all? ¿world?</p>',
            ],
            [
                '<p>¿ Hello world?</p>',
                '<p>¿Hello world?</p>',
            ],
            [
                'Hello ¿ <strong>world?</strong>',
                'Hello ¿<strong>world?</strong>',
            ],
            [
                'Hello ¿<strong> world?</strong>',
                'Hello ¿<strong>world?</strong>',
            ],
            [
                'Hello ( world)',
                'Hello (world)',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::fix($text, [
            new RemoveSpaceAfter(),
        ]);

        $this->assertSame($expect, $result);
    }
}
