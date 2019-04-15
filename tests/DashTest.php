<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Typofixer;
use Typofixer\Fixers\Dash;

class DashTest extends TestCase
{
    public function dataProvider()
    {
        return [
            [
                '<p>123-456</p>',
                '<p>123–456</p>',
            ],
            [
                '<p>123--456</p>',
                '<p>123—456</p>',
            ],
            [
                '<p>abc-abc</p>',
                '<p>abc-abc</p>',
            ],
            [
                '<p>Hello --world--</p>',
                '<p>Hello —world—</p>',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFixer($text, $expect)
    {
        $result = Typofixer::runFixers($text, new Dash());

        $this->assertSame($expect, $result);
    }
}
