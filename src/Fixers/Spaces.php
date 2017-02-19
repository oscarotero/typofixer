<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Fix the following spaces issues:
 *
 * - normalize space characters
 * - remove duplicated spaces
 * - normalize spaces in the start/end of some nodes
*/
class Spaces implements FixerInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(Fixer $fixer)
	{
		$prev = null;

		foreach ($fixer->textNodes() as $node) {
			$node->data = preg_replace('/[\s]+/u', ' ', $node->data);

			if (substr($node->data, 0, 1) === ' ') {
				if (!$prev) {
					$node->data = substr($node->data, 1);
				} elseif (substr($prev->data, -1) === ' ') {
					$prev->data = substr($prev->data, 0, -1);
				}
			}

			$prev = $node;
		}
	}
}