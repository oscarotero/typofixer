<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;
use DOMNode;

/**
 * Remove tags inside other tags
 */
class RemoveInnerTags extends Fixer
{
    const PRIORITY = 10;

    private $tags = [
        'strong' => ['strong', 'b'],
        'em'     => ['em', 'i'],
        'a'      => ['a'],
        'p'      => ['p'],
    ];

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        foreach ($html->nodes(XML_ELEMENT_NODE) as $node) {
            $tags = $this->tags[$node->tagName] ?? null;

            if ($tags && self::hasParent($node, $tags)) {
                self::unwrapNode($node);
            }
        }
    }

    private static function hasParent(DOMNode $node, array $types)
    {
        while ($node = $node->parentNode) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                return false;
            }

            if (in_array($node->tagName, $types)) {
                return true;
            }
        }

        return false;
    }

    private static function unwrapNode(DOMNode $node)
    {
        foreach ($node->childNodes as $child) {
            $node->parentNode->insertBefore($child);
        }

        $node->parentNode->removeChild($node);
    }

    public function getPriority(): int
    {
        return self::PRIORITY;
    }
}
