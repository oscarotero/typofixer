<?php
declare(strict_types=1);

namespace Typofixer;

use DOMText;

/**
 * Class with some utils for DOM manipulation
 */
final class Utils
{
	public static function endsWith(DOMText $node = null, $char): bool
	{
		return $node && mb_substr($node->data, -1) === $char;
	}

	public static function startsWith(DOMText $node = null, $char): bool
	{
		return $node && mb_substr($node->data, 0, 1) === $char;
	}
}