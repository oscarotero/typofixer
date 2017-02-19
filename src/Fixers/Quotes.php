<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Fix the open/close quotes
 * - Replace plain quotes by curly quotes
 * - Fixes some quotes positions:
 *   <b>“Hello</b>” world -> <b>“Hello”</b> world
 *   “<b>Hello”</b> world -> <b>“Hello”</b> world
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
		$prev = null;

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

			if (Utils::endsWith($prev, $this->quotes[0])) {
				$prev->data = mb_substr($prev->data, 0, -1);
				$text = $this->quotes[0].$text;
			}

			$node->data = $text;

			if (Utils::startsWith($node, $this->quotes[1])) {
				$prev->data .= $this->quotes[1];
				$node->data = mb_substr($node->data, 1);
			}

			$prev = $node;
		}
	}
}