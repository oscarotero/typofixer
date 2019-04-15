<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

/**
 * Fix the open/close quotes and replace plain quotes by curly quotes
 */
class Quotes extends Fixer
{
    const PRIORITY = 7;

    const SINGLE_ANGULAR_LEFT = '‹';
    const SINGLE_ANGULAR_RIGHT = '›';
    const DOUBLE_ANGULAR_LEFT = '«';
    const DOUBLE_ANGULAR_RIGHT = '»';
    const SINGLE_CURVED_LEFT = '‘';
    const SINGLE_CURVED_RIGHT = '’';
    const DOUBLE_CURVED_LEFT = '“';
    const DOUBLE_CURVED_RIGHT = '”';
    const DOUBLE_CURVED_BOTTOM = '„';
    const SINGLE_CURVED_BOTTOM = '‚';

    const APOSTROPHE = '’';

    const LANGUAGES = [
        [
            'languages' => ['en'],
            'primary' => [self::DOUBLE_CURVED_LEFT, self::DOUBLE_CURVED_RIGHT],
            'secondary' => [self::SINGLE_CURVED_LEFT, self::SINGLE_CURVED_RIGHT],
        ],
        [
            'languages' => ['es', 'fr', 'gl', 'pt'],
            'primary' => [self::DOUBLE_ANGULAR_LEFT, self::DOUBLE_ANGULAR_RIGHT],
            'secondary' => [self::DOUBLE_CURVED_LEFT, self::DOUBLE_CURVED_RIGHT],
        ],
        [
            'languages' => ['de', 'nl'],
            'primary' => [self::DOUBLE_CURVED_BOTTOM, self::DOUBLE_CURVED_LEFT],
            'secondary' => [self::SINGLE_CURVED_BOTTOM, self::SINGLE_CURVED_LEFT],
        ],
        [
            'languages' => ['sv'],
            'primary' => [self::DOUBLE_CURVED_RIGHT, self::DOUBLE_CURVED_RIGHT],
            'secondary' => [self::SINGLE_CURVED_RIGHT, self::SINGLE_CURVED_RIGHT],
        ],
        [
            'languages' => ['da'],
            'primary' => [self::DOUBLE_ANGULAR_RIGHT, self::DOUBLE_ANGULAR_LEFT],
            'secondary' => [self::SINGLE_ANGULAR_RIGHT, self::SINGLE_ANGULAR_LEFT],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        list($primary, $secondary) = $this->getLanguageQuotes();

        $quotes = [];
        $prev = null;

        $opening = [
            self::SINGLE_ANGULAR_LEFT,
            self::DOUBLE_ANGULAR_LEFT,
            self::SINGLE_CURVED_LEFT,
            self::DOUBLE_CURVED_LEFT,
        ];
        $closing = [
            self::SINGLE_ANGULAR_RIGHT,
            self::DOUBLE_ANGULAR_RIGHT,
            self::SINGLE_CURVED_RIGHT,
            self::DOUBLE_CURVED_RIGHT,
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
                    $text = rtrim($text).(isset($quotes[0]) ? $secondary[1] : $primary[1]);
                    continue;
                }

                //Found a possible apostrophe
                if (self::isApostrophe($char, $prevChar)) {
                    $text .= self::APOSTROPHE;
                    continue;
                }

                //Found an opening quote
                if (($i = array_search($char, $opening)) !== false) {
                    array_unshift($quotes, $closing[$i]);
                    $text .= isset($quotes[1]) ? $secondary[0] : $primary[0];

                    //remove spaces after opening quote
                    while (mb_substr($node->data, $k + 1, 1) === ' ') {
                        ++$k;
                    }
                    continue;
                }

                //Found an opening flat quote
                if (self::isOpeningFlatQuote($char)) {
                    array_unshift($quotes, $char);
                    $text .= isset($quotes[1]) ? $secondary[0] : $primary[0];

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
     */
    private static function isOpeningFlatQuote(string $char): bool
    {
        return ($char === '"' || $char === '´' || $char === "'");
    }

    /**
     * Check whether the character is an apostrophe (ex: it's)
     */
    private static function isApostrophe(string $char, string $prevChar = null): bool
    {
        return ($char === "'" || $char === '’' || $char === '´')
          && ($prevChar !== null)
          && preg_match('/^\w$/u', $prevChar);
    }

    /**
     * Check whether the character is the closing quote of the previous opened quote
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

        if (in_array($current, ["'", '’']) && in_array($char, ["'", '‘', '’', '´'])) {
            return !self::isApostrophe($char, $prevChar);
        }

        return false;
    }

    private function getLanguageQuotes(): array
    {
        $code = $this->options['language'] ?? 'en';

        foreach (self::LANGUAGES as $language) {
            if (in_array($code, $language['languages'])) {
                return [
                    $language['primary'],
                    $language['secondary'],
                ];
            }
        }
    }
}
