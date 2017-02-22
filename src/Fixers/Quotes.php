<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Fix the open/close quotes
 * - Replace plain quotes by curly quotes
 * - Fixes some quotes positions:
 *   <b>“Hello</b>” world -> <b>“Hello”</b> world
 *   “<b>Hello”</b> world -> <b>“Hello”</b> world
 */
class Quotes implements FixerInterface
{
    const SINGLE_ANGULAR = ['‹', '›'];
    const DOUBLE_ANGULAR = ['«', '»'];
    const SINGLE_CURVED = ['‘', '’'];
    const DOUBLE_CURVED = ['“', '”'];

    private $primary;
    private $secondary;

    public function __construct(array $primary = null, array $secondary = null)
    {
        $this->primary = $primary === null ? self::DOUBLE_ANGULAR : $primary;
        $this->secondary = $secondary === null ? self::DOUBLE_CURVED : $secondary;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $deep = [];
        $prev = null;

        $opening = [
            self::SINGLE_ANGULAR[0],
            self::DOUBLE_ANGULAR[0],
            self::SINGLE_CURVED[0],
            self::DOUBLE_CURVED[0],
        ];
        $closing = [
            self::SINGLE_ANGULAR[1],
            self::DOUBLE_ANGULAR[1],
            self::SINGLE_CURVED[1],
            self::DOUBLE_CURVED[1],
        ];

        foreach ($fixer->nodes(XML_TEXT_NODE) as $node) {
            $text = '';
            $length = strlen($node->data);

            for ($k = 0; $k < $length; $k++) {
                $prevChar = $char ?? null;
                $char = mb_substr($node->data, $k, 1);

                if (isset($deep[0]) && ($deep[0] === $char || array_search($char, $closing) !== false)) {
                    array_shift($deep);
                    //remove spaces before closing quote
                    $text = rtrim($text).(isset($deep[0]) ? $this->secondary[1] : $this->primary[1]);
                    continue;
                }

                //new opening quote
                if (($i = array_search($char, $opening)) !== false) {
                    array_unshift($deep, $closing[$i]);
                    $text .= isset($deep[1]) ? $this->secondary[0] : $this->primary[0];

                    //remove spaces after opening quote
                    while (mb_substr($node->data, $k + 1, 1) === ' ') {
                        ++$k;
                    }
                    continue;
                }

                //new flat quote
                if ($char === '"' || $char === '´') {
                    array_unshift($deep, $char);
                    $text .= isset($deep[1]) ? $this->secondary[0] : $this->primary[0];

                    //remove spaces after opening quote
                    while (mb_substr($node->data, $k + 1, 1) === ' ') {
                        ++$k;
                    }
                    continue;
                }

                //new simple quote (discard apostrophes)
                if ($char === "'" && ($k === 0 || ($prevChar && !preg_match('/^[a-z]$/i', $prevChar)))) {
                    array_unshift($deep, "'");
                    $text .= isset($deep[1]) ? $this->secondary[0] : $this->primary[0];

                    //remove spaces after opening quote
                    while (mb_substr($node->data, $k + 1, 1) === ' ') {
                        ++$k;
                    }
                    continue;
                }

                $text .= $char;
            }

            if (Utils::endsWith($prev, $this->primary[0])) {
                $prev->data = mb_substr($prev->data, 0, -1);
                $text = $this->primary[0].$text;
            }

            $node->data = $text;

            if (Utils::startsWith($node, $this->primary[1])) {
                $prev->data .= $this->primary[1];
                $node->data = mb_substr($node->data, 1);
            }

            $prev = $node;
        }
    }
}
