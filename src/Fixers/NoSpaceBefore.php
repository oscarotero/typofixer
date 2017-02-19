<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Removes the space before some characters like ,:;!?
 */
class NoSpaceBefore implements FixerInterface
{
	private $chars;

	public function __construct(string $chars = ',:;!?')
	{
		$this->chars = $chars;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __invoke(Fixer $fixer)
	{
		$regexp = "/\s([{$this->chars}])/";

		foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
			$node->data = preg_replace($regexp, '$1', $node->data);
		}
	}
}