<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Replace multiple dots by ellipsis
*/
class Ellipsis implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $count = 0;

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            if ($count !== 0) {
                $node->data = ltrim($node->data, '.');
            }

            $node->data = preg_replace('/\.{2,}/', '…', $node->data, -1, $count);
        }
    }
}
