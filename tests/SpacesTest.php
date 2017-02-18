<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Fixer;
use Typofixer\Fixers\Spaces;

class SpacesTest extends TestCase
{
	public function nbspProvider()
	{
		return [
			[
				'<p>Hello&nbsp;&nbsp;world</p>',
				'<p>Hello world</p>',
			],
			[
				'<p><strong>Hello   </strong>  world</p>',
				'<p><strong>Hello</strong> world</p>',
			],
			[
				'<p><strong> Hello </strong> world</p>',
				'<p><strong>Hello</strong> world</p>',
			],
			[
				'<p><strong> Hello, </strong>world</p>',
				'<p><strong>Hello,</strong> world</p>',
			]
		];
	}

	/**
	 * @dataProvider nbspProvider
	 */
	public function testNbsp($text, $expect)
	{
		$result = Fixer::fix($text, [
			new Spaces(),
		]);

		$this->assertSame($expect, $result);
	}
}