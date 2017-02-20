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
