<?php
declare(strict_types=1);

namespace Typofixer;

use InvalidArgumentException;

/**
 * Exception throwed by the fixers if the text cannot be safety fixed by any reason
 */
class InvalidTextException extends InvalidArgumentException
{

}