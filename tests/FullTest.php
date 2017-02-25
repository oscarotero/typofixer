<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\Spaces;

class FullTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '« <em>Search..</em>. ».',
                '<em>«Search…».</em>',
            ],
            [
                '<p>Hello,<strong>world</strong></p>',
                '<p>Hello, <strong>world</strong></p>',
            ],
            [
                '<p><strong>Hello,</strong>world</p>',
                '<p><strong>Hello,</strong> world</p>',
            ],
            [
                '<p>Hello ‘world’</p>',
                '<p>Hello «world»</p>',
            ],
            [
                '¿<strong> Hello </strong> ? world',
                '<strong>¿Hello?</strong> world',
            ],
            [
                '<p>Hello ‘<strong>world</strong>’</p>',
                '<p>Hello <strong>«world»</strong></p>',
            ],
            [
                "<p>Suyas</p>\n\n<p><strong><em>\"El\"</em></strong></p>",
                "<p>Suyas</p> <p><strong><em>«El»</em></strong></p>",
            ],
            [
                '<p>Hello <strong>pre-<i>world</i></strong> <strong>all</strong></p>',
                '<p>Hello <strong>pre-<i>world</i> all</strong></p>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::fix($text);

        $this->assertSame($expect, $result);
    }
}
