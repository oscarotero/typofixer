<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;
use DOMNode;

/**
 * Remove empty tags or tags containing only spaces
 */
class RemoveEmptyTags implements FixerInterface
{
	private $tags = ['strong', 'em', 'b', 'i'];

	/**
	 * {@inheritdoc}
	 */
	public function __invoke(Fixer $fixer)
	{
		$toRemove = [];

		foreach ($fixer->nodes(XML_ELEMENT_NODE) as $node) {
			if (in_array($node->tagName, $this->tags)) {
				if (trim($node->textContent) === '') {
					$toRemove[] = $node;
				}
			}
		}

		foreach ($toRemove as $node) {
			if ($node->textContent !== '' && $node->previousSibling) {
				$node->previousSibling->textContent .= ' ';
			}

			$node->parentNode->removeChild($node);
		}
	}
}