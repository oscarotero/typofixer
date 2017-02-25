<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

/**
 * Fix the open/close quotes and replace plain quotes by curly quotes
 */
class Quotes extends Fixer
{
    const SINGLE_ANGULAR = ['‹', '›'];
    const DOUBLE_ANGULAR = ['«', '»'];
    const SINGLE_CURVED = ['‘', '’'];
    const DOUBLE_CURVED = ['“', '”'];
    const APOSTROPHE = '’';

    private $primary = self::DOUBLE_ANGULAR;
    private $secondary = self::DOUBLE_CURVED;
    private $apostrophe = self::APOSTROPHE;

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
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

        foreach ($html->nodes(XML_TEXT_NODE) as $node) {
            $text = '';
            $length = mb_strlen($node->data);

            for ($k = 0; $k < $length; $k++) {
                $prevChar = $char ?? null;
                $char = mb_substr($node->data, $k, 1);

                if (self::isClosingQuote($quotes, $char, $prevChar)) {
                    array_shift($quotes);

                    //remove spaces before closing quote
                    $text = rtrim($text).(isset($quotes[0]) ? $this->secondary[1] : $this->primary[1]);
                    continue;
                }

                //Found a possible apostrophe
                if (self::isApostrophe($char, $prevChar)) {
                    $text .= $this->apostrophe;
                    continue;
                }

                //Found an opening quote
                if (($i = array_search($char, $opening)) !== false) {
                    array_unshift($quotes, $closing[$i]);
                    $text .= isset($quotes[1]) ? $this->secondary[0] : $this->primary[0];

                    //remove spaces after opening quote
                    while (mb_substr($node->data, $k + 1, 1) === ' ') {
                        ++$k;
                    }
                    continue;
                }

                //Found an opening flat quote
                if (self::isOpeningFlatQuote($char)) {
                    array_unshift($quotes, $char);
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

        if (!empty($quotes)) {
            $this->errorLog('Found some unclosed quotes');
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
     * @var string|null $prevChar
     *
     * @return bool
     */
    private static function isApostrophe(string $char, string $prevChar = null): bool
    {
        return ($char === "'" || $char === '’' || $char === '´')
          && ($prevChar !== null)
          && preg_match('/^\w$/u', $prevChar);
    }

    /**
     * Check whether the character is the closing quote of the previous opened quote
     *
     * @param array $quotes
     * @param string $char
     * @param string $prevChar
     *
     * @return bool
     */
    private static function isClosingQuote(array $quotes, string $char, string $prevChar = null): bool
    {
        if (empty($quotes)) {
            return false;
        }

        $current = $quotes[0];

        if ($current === $char) {
            return true;
        }

        if (in_array($current, ['"', '”']) && in_array($char, ['"', '“', '”'])) {
            return true;
        }

        if (in_array($current, ["'", '’']) && in_array($char, ["'", '‘', '’'])) {
            return ($prevChar === null) || !preg_match('/^\w$/iu', $prevChar);
        }

        return false;
    }
}
