<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Replace multiple dots by ellipsis
*/
class Ellipsis implements FixerInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(Fixer $fixer)
	{
		foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
			$node->data = preg_replace('/\.{3,}/', '…', $node->data);
		}
	}
}