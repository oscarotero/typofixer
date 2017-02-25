<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Ensures there's a space after some characters like ,:;!?
 */
class AddSpaceAfter implements FixerInterface
{
    private $chars;
    private $spaces;

    public function __construct(string $chars = '.,:;!?', string $spaces = '.!?)')
    {
        $this->chars = $chars;
        $this->spaces = $spaces;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $chars = preg_quote($this->chars, '/');
        $spaces = preg_quote($this->spaces, '/');

        $regexpContains = "/([{$chars}])([^\s\d{$spaces}])/u";
        $regexpEnds = "/[{$chars}]$/u";
        $regexpStarts = "/^[^\s\d{$spaces}]/u";
        $prev = null;

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace($regexpContains, '$1 $2', $node->data);

            if ($prev && preg_match($regexpEnds, $prev->data) && preg_match($regexpStarts, $node->data)) {
                $node->data = ' '.$node->data;
            }

            $prev = $node;
        }
    }
}
