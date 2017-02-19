<?php
declare(strict_types=1);

namespace Typofixer\Tests;

use PHPUnit\Framework\TestCase;
use Typofixer\Fixer;
use Typofixer\Fixers\NoSpaceBefore;

class NoSpaceBeforeTest extends TestCase
{
	public function dataProvider()
	{
		return [
			[
				'<p>Hello , world</p>',
				'<p>Hello, world</p>',
			],
			[
				'How are you ?',
				'How are you?',
			],
		];
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testFixer($text, $expect)
	{
		$result = Fixer::fix($text, [
			new NoSpaceBefore(),
		]);

		$this->assertSame($expect, $result);
	}
}