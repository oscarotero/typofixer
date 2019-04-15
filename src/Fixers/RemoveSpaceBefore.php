<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

/**
 * Removes the space before some characters
 */
class RemoveSpaceBefore extends Fixer
{
    const PRIORITY = 4;

    private $chars = '.,:;!?…)';

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        $chars = preg_quote($this->chars, '/');
        $regexpContains = "/\s+([{$chars}])/u";
        $regexpStarts = "/^([{$chars}]+)/u";
        $prev = null;

        foreach ($html->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace($regexpContains, '$1', $node->data);

            if ($prev && preg_match($regexpStarts, $node->data)) {
                $prev->data = rtrim($prev->data);
            }

            $prev = $node;
        }
    }
}
