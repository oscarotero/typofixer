<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Removes the space before some characters like ,:;!?
 */
class RemoveSpaceBefore implements FixerInterface
{
    private $chars;

    public function __construct(string $chars = '.,:;!?…)')
    {
        $this->chars = $chars;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $chars = preg_quote($this->chars, '/');
        $regexpContains = "/\s+([{$chars}])/u";
        $regexpStarts = "/^([{$chars}]+)/u";
        $prev = null;

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace($regexpContains, '$1', $node->data);

            if ($prev && preg_match($regexpStarts, $node->data)) {
                $prev->data = rtrim($prev->data);
            }

            $prev = $node;
        }
    }
}
