<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

/**
 * Removes the space after some characters
 */
class RemoveSpaceAfter extends Fixer
{
    const PRIORITY = 5;

    private $chars = '(¿¡';

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        $chars = preg_quote($this->chars, '/');
        $regexpContains = "/([{$chars}])\s+/u";
        $regexpEnds = "/([{$chars}]+)$/u";
        $prev = null;

        foreach ($html->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace($regexpContains, '$1', $node->data);

            if ($prev && preg_match($regexpEnds, $prev->data)) {
                $node->data = ltrim($node->data);
            }

            $prev = $node;
        }
    }


    public function getPriority(): int
    {
        return self::PRIORITY;
    }
}
