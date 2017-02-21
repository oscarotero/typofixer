<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Removes the space before some characters like ,:;!?
 */
class NoSpaceBefore implements FixerInterface
{
    private $regexp;

    public function __construct(string $chars = ',:;!?')
    {
        $this->regexp = '/\s+(['.preg_quote($chars).'])/';
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace($this->regexp, '$1', $node->data);
        }
    }
}
