<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Fixer;
use Typofixer\Fixers\Spaces;

class FullTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '« <em>Search..</em>. ».',
                '<em>«Search…»</em>.',
            ],
            [
                '<p>Hello,<strong>world</strong></p>',
                '<p>Hello, <strong>world</strong></p>',
            ],
            [
                '<p><strong>Hello,</strong>world</p>',
                '<p><strong>Hello,</strong> world</p>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Fixer::fix($text);

        $this->assertSame($expect, $result);
    }
}
