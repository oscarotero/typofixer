<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Fix certain space positions, for example:
 *   <b>Hello </b>world -> <b>Hello</b> world
 *   <b>Hello </b><i>world</i> -> <b>Hello</b> <i>world</i>
 */
class SpaceTags implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $trim = false;
        $prev = null;
        $toRemove = [];

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            $isUniqueChild = self::isUniqueChild($node);
            $startsWithSpace = Utils::startsWith($node, ' ');

            if ($startsWithSpace && $prev && $isUniqueChild) {
                $node->data = ltrim($node->data);

                if (!Utils::endsWith($prev, ' ')) {
                    $prev->data .= ' ';
                }
            }

            if ($trim && !$startsWithSpace && !self::isFirstInBlock($node)) {
                if ($isUniqueChild) {
                    $node->parentNode->parentNode->insertBefore(new DOMText(' '), $node->parentNode);
                } else {
                    $node->data = ' '.$node->data;
                }
            }
            
            $trim = false;

            if ($node->data === ' ') {
                $toRemove[] = $node;
                $trim = true;
                continue;
            }

            if ($isUniqueChild && Utils::endsWith($node, ' ')) {
                $node->data = rtrim($node->data);
                $trim = true;
            }

            $prev = $node;
        }

        foreach ($toRemove as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    private static function isUniqueChild($node)
    {
        $parent = $node->parentNode;

        return $parent && ($parent->firstChild === $parent->lastChild);
    }

    private static function isFirstInBlock($node)
    {
        $parent = $node;

        while ($parent->parentNode) {
            if (Utils::isBlock($parent)) {
                return Utils::getFirstNodeText($parent) === $node;
            }

            $parent = $parent->parentNode;

        }
    }
}
