<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;
use Typofixer\Utils;
use DOMNode;

/**
 * Merge consecutive tags. Example:
 * <strong>hello</strong> <strong>world</strong>
 * becomes to:
 * <strong>hello world</strong>
 */
class MergeTags extends Fixer
{
    const PRIORITY = 8;

    private $tags = ['strong', 'em', 'b', 'i'];

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        foreach ($html->nodes(XML_ELEMENT_NODE) as $node) {
            while (in_array($node->tagName, $this->tags) && $node->nextSibling) {
                if (self::isMergeable($node, $node->nextSibling)) {
                    Utils::mergeNodes($node, $node->nextSibling);
                    continue;
                }

                //Remove space betwen two tags
                if (
                    $node->nextSibling->nodeType === XML_TEXT_NODE
                    && trim($node->nextSibling->data) === ''
                    && $node->nextSibling->nextSibling
                    && self::isMergeable($node, $node->nextSibling->nextSibling)
                ) {
                    Utils::mergeNodes($node, $node->nextSibling, $node->nextSibling->nextSibling);
                    continue;
                }

                break;
            }
        }
    }

    private static function isMergeable(DOMNode $current, DOMNode $next)
    {
        return $next->nodeType === XML_ELEMENT_NODE && $next->tagName === $current->tagName;
    }
}
