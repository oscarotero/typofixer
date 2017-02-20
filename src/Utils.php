<?php
declare(strict_types=1);

namespace Typofixer;

use DOMText;
use DOMNode;

/**
 * Class with some utils for DOM manipulation
 */
final class Utils
{
    public static function endsWith(DOMText $node = null, $char): bool
    {
        return $node && mb_substr($node->data, -1) === $char;
    }

    public static function startsWith(DOMText $node = null, $char): bool
    {
        return $node && mb_substr($node->data, 0, 1) === $char;
    }

    public static function getFirstNodeText(DOMNode $node)
    {
        while ($node && $node->nodeType !== XML_TEXT_NODE) {
            $node = $node->firstChild;
        }

        return $node;
    }

    public static function mergeNodes(DOMNode $node, DOMNode $next)
    {
        foreach ($next->childNodes as $child) {
            $node->appendChild($child);
        }

        $next->parentNode->removeChild($next);
    }
}
