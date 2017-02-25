<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\InvalidTextException;

/**
 * Abstract fixer extended by the other fixers
 */
abstract class Fixer implements FixerInterface
{
	protected $options;

	public function __construct(array $options = null)
	{
		$this->options = $options ?: [];
	}

	protected function errorLog($message)
	{
		if (empty($this->options['debug'])) {
			throw new InvalidTextException($message);
		}
	}
}
