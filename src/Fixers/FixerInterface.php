<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

interface FixerInterface
{
    public function __construct(array $options);

    public function __invoke(Typofixer $html);

    public function getPriority(): int;
}
