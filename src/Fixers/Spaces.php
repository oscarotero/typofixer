<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Fix the following spaces issues:
 *
 * - normalize space characters
 * - remove duplicated spaces
 * - fixes certain space positions:
 *   <b>Hello </b>world -> <b>Hello</b> world
 *   <b>Hello </b><i>world</i> -> <b>Hello</b> <i>world</i>
 */
class Spaces implements FixerInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(Fixer $fixer)
	{
		$trim = false;

		foreach ($fixer->textNodes() as $node) {
			$node->data = preg_replace('/[\s]+/u', ' ', $node->data);

			if ($trim && !Utils::startsWith($node, ' ')) {
				if (self::isUniqueChild($node)) {
					$node->parentNode->parentNode->insertBefore(new DOMText(' '), $node->parentNode);
				} else {
					$node->data = ' '.$node->data;
				}
			}

			$trim = false;

			if ($node->data === ' ') {
				continue;
			}

			if (self::isUniqueChild($node) && Utils::endsWith($node, ' ')) {
				$node->data = rtrim($node->data);
				$trim = true;
			}
		}
	}

	private static function isUniqueChild($node)
	{
		$parent = $node->parentNode;

		return $parent && ($parent->firstChild === $parent->lastChild);
	}
}