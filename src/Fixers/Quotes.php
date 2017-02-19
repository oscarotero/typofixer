<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Fix the open/close quotes
*/
class Quotes implements FixerInterface
{
	private $quotes = ['“', '”'];

	/**
	 * {@inheritdoc}
	 */
	public function __invoke(Fixer $fixer)
	{
		$deep = [];

		foreach ($fixer->textNodes() as $node) {
			$text = '';
			$length = strlen($node->data);

			for ($k = 0; $k < $length; $k++) {
				$char = $node->data[$k];

				if (isset($deep[0]) && $deep[0] === $char) {
					$text .= $this->quotes[1];
					continue;
				}

				if ($char === '"') {
					$deep[] = '"';
					$text .= $this->quotes[0];
					continue;
				}

				$text .= $char;
			}

			$node->data = $text;
		}
	}
}