<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

/**
 * Ensures there's a space after some characters like ,:;!?
 */
class AddSpaceAfter extends Fixer
{
    private $chars = '.,:;!?';
    private $spaces = '.!?)';

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        $chars = preg_quote($this->chars, '/');
        $spaces = preg_quote($this->spaces, '/');

        $regexpContains = "/([{$chars}])([^\s\d{$spaces}])/u";
        $regexpEnds = "/[{$chars}]$/u";
        $regexpStarts = "/^[^\s\d{$spaces}]/u";
        $prev = null;

        foreach ($html->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace($regexpContains, '$1 $2', $node->data);

            //fix for domains (ex: domain. com => domain.com)
            $node->data = preg_replace('/([a-z])\. ([a-z])/', '$1.$2', $node->data);

            if ($prev && preg_match($regexpEnds, $prev->data) && preg_match($regexpStarts, $node->data)) {
                $node->data = ' '.$node->data;
            }

            $prev = $node;
        }
    }
}
