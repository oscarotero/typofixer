<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;

interface FixerInterface
{
    public function __invoke(Fixer $fixer);
}
