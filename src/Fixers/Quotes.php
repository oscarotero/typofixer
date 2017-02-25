<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Fixer;
use Typofixer\Utils;
use DOMText;

/**
 * Fix the open/close quotes
 * - Replace plain quotes by curly quotes
 */
class Quotes implements FixerInterface
{
    const SINGLE_ANGULAR = ['‹', '›'];
    const DOUBLE_ANGULAR = ['«', '»'];
    const SINGLE_CURVED = ['‘', '’'];
    const DOUBLE_CURVED = ['“', '”'];
    const APOSTROPHE = '’';

    private $primary;
    private $secondary;

    public function __construct(array $primary = null, array $secondary = null, string $apostrophe = null)
    {
        $this->primary = $primary === null ? self::DOUBLE_ANGULAR : $primary;
        $this->secondary = $secondary === null ? self::DOUBLE_CURVED : $secondary;
        $this->apostrophe = $apostrophe === null ? self::APOSTROPHE : $apostrophe;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Fixer $fixer)
    {
        $quotes = [];
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
            $length = mb_strlen($node->data);

            for ($k = 0; $k < $length; $k++) {
                $prevChar = $char ?? null;
                $char = mb_substr($node->data, $k, 1);

                //Found a (previously opened) closing quote
                if (isset($quotes[0]) && in_array($char, $quotes[0])) {
                    array_shift($quotes);
                    //remove spaces before closing quote
                    $text = rtrim($text).(isset($quotes[0]) ? $this->secondary[1] : $this->primary[1]);
                    continue;
                }

                //Found a possible apostrophe
                if (self::isApostrophe($char, $k, $prevChar)) {
                    $text .= $this->apostrophe;
                    continue;
                }

                //Found a (non previously opened) closing quote
                if (isset($quotes[0]) && (in_array($char, $quotes[0]) || in_array($char, $closing) !== false)) {
                    array_shift($quotes);
                    //remove spaces before closing quote
                    $text = rtrim($text).(isset($quotes[0]) ? $this->secondary[1] : $this->primary[1]);
                    continue;
                }

                //Found an opening quote
                if (($i = array_search($char, $opening)) !== false) {
                    array_unshift($quotes, [$closing[$i], $opening[$i]]);
                    $text .= isset($quotes[1]) ? $this->secondary[0] : $this->primary[0];

                    //remove spaces after opening quote
                    while (mb_substr($node->data, $k + 1, 1) === ' ') {
                        ++$k;
                    }
                    continue;
                }

                //Found a flat quote (not sure if its opening or closing)
                if (self::isOpeningFlatQuote($char)) {
                    array_unshift($quotes, [$char]);
                    $text .= isset($quotes[1]) ? $this->secondary[0] : $this->primary[0];

                    //remove spaces after opening quote
                    while (mb_substr($node->data, $k + 1, 1) === ' ') {
                        ++$k;
                    }
                    continue;
                }

                $text .= $char;
            }

            $node->data = $text;
            $prev = $node;
        }
    }

    /**
     * Check whether the character is an open flat quote
     *
     * @var string $char
     *
     * @return bool
     */
    private static function isOpeningFlatQuote(string $char): bool
    {
        return ($char === '"' || $char === '´' || $char === "'");
    }

    /**
     * Check whether the character is an apostrophe (ex: it's)
     *
     * @var string $char
     * @var int $position
     * @var string|null $prevChar
     * @var string $nextChar
     *
     * @return bool
     */
    private static function isApostrophe(string $char, int $position, string $prevChar = null, string $nextChar = ''): bool
    {
        return ($char === "'" || $char === '’')
          && ($position > 0)
          && $prevChar && preg_match('/^[a-z]$/iu', $prevChar);
          //&& ($nextChar !== '') && preg_match('/^[a-z]$/iu', $nextChar);
    }
}
