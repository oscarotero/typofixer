<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

/**
 * Fix the following spaces issues:
 * - normalize space characters
 * - remove duplicated spaces
 */
class Spaces extends Fixer
{
    const PRIORITY = 1;

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        foreach ($html->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace('/[\s]+/u', ' ', $node->data);
        }
    }
}
