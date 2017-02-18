<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

class Spaces implements FixerInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(Fixer $fixer)
	{
		$prev = null;

		foreach ($fixer->textNodes() as $node) {
			$node->data = preg_replace('/[\s]{2,}/u', ' ', $node->data);

			if (substr($node->data, 0, 1) === ' ') {
				if (!$prev) {
					$node->data = substr($node->data, 1);
				} elseif (substr($prev->data, -1) === ' ') {
					$prev->data = substr($prev->data, 0, -1);
				}
			} elseif ($prev && substr($prev->data, -1) === ' ') {
				$prev->data = substr($prev->data, 0, -1);
				$node->data = ' '.$node->data;
			}

			$prev = $node;
		}
	}
}