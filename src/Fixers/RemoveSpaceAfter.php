<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Removes the space after some characters
 */
class RemoveSpaceAfter implements FixerInterface
{
    private $chars;

    public function __construct(string $chars = '(¿¡')
    {
        $this->chars = $chars;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $chars = preg_quote($this->chars, '/');
        $regexpContains = "/([{$chars}])\s+/u";
        $regexpEnds = "/([{$chars}]+)$/u";
        $prev = null;

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace($regexpContains, '$1', $node->data);

            if ($prev && preg_match($regexpEnds, $prev->data)) {
                $node->data = ltrim($node->data);
            }

            $prev = $node;
        }
    }
}
