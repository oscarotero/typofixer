<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use DOMText;

/**
 * Move some chars inside some tags. Ex:
 * <strong>hello</strong>. -> <strong>hello.</strong>
 */
class CharsInside implements FixerInterface
{
    private $ends = '.,:;!?…)»›’”';
    private $starts = '‹«‘“¿¡';
    private $tags = ['strong', 'em', 'b', 'i'];

    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $ends = preg_quote($this->ends, '/');
        $starts = preg_quote($this->starts, '/');

        $regexpStarts = "/^([{$ends}]+)/u";
        $regexpEnds = "/([{$starts}]+)$/u";
        $prev = null;

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            //ex: hello «<b>world»</b> -> hello <b>«world»</b>
            if ($prev && preg_match($regexpEnds, $prev->data, $match)) {
                $prev->data = ($prev->data === $match[0]) ? '' : mb_substr($prev->data, 0, -mb_strlen($match[0]));
                $node->data = $match[0].ltrim($node->data);
            }

            //ex: hello <b>«world</b>» -> hello <b>«world»</b>
            if ($prev && preg_match($regexpStarts, $node->data, $match)) {
                $node->data = mb_substr($node->data, mb_strlen($match[0]));
                $prev->data = rtrim($prev->data).$match[0];
            }

            $prev = $node;
        }
    }
}
