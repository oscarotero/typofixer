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
        $prev = null;

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            //If the previous node has ellipsis, remove the possible left dots.
            //<strong>hello...</strong>. -> <strong>hello…</strong>
            if ($count !== 0) {
                $node->data = ltrim($node->data, '.');
            }

            $node->data = preg_replace('/\.{2,}/', '…', $node->data, -1, $count);

            //Fix ellipsis out of the tag:
            //<strong>hello</strong>… -> <strong>hello…</strong>
            if ($prev && mb_substr($node->data, 0, 1) === '…') {
                $prev->data .= '…';
                $node->data = mb_substr($node->data, 1);
            }

            $prev = $node;
        }
    }
}
