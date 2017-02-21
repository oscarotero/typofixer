<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Fix the following spaces issues:
 * - normalize space characters
 * - remove duplicated spaces
 */
class Spaces implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace('/[\s]+/u', ' ', $node->data);
        }
    }
}
