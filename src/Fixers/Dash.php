<?php
declare(strict_types=1);

namespace Typofixer\Fixers;

use Typofixer\Typofixer;

/**
 * Fixes dashes
 * Adapted from https://github.com/jolicode/JoliTypo/blob/master/src/JoliTypo/Fixer/Dash.php
*/
class Dash extends Fixer
{
    const PRIORITY = 12;

    const NDASH = '–';
    const MDASH = '—';

    /**
     * {@inheritdoc}
     */
    public function __invoke(Typofixer $html)
    {
        foreach ($html->nodes(XML_TEXT_NODE) as $node) {
            $node->data = preg_replace(
                ['/(?<=[0-9 ]|^)-(?=[0-9 ]|$)/', '/--([^-]|$)/s'],
                [self::NDASH, self::MDASH.'$1'],
                $node->data
            );
        }
    }

    public function getPriority(): int
    {
        return self::PRIORITY;
    }
}
