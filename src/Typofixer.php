<?php
declare(strict_types=1);

namespace Typofixer;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use Typofixer\Fixers\FixerInterface;

class Typofixer
{
    public $debug = false;

    private $dom;
    private $config = [];

    public static function create(string $content): self
    {
        return new static(self::createDOMDocument($content));
    }

    public static function runFixers(string $content, FixerInterface ...$fixers): string
    {
        $self = self::create($content);
        $self(...$fixers);

        return (string) $self;
    }

    public static function fix(string $content, array $options = []): string
    {
        return self::runFixers(
            $content,
            new Fixers\Spaces($options),
            new Fixers\AddSpaceAfter($options),
            new Fixers\Ellipsis($options),
            new Fixers\RemoveSpaceBefore($options),
            new Fixers\RemoveSpaceAfter($options),
            new Fixers\SpaceTags($options),
            new Fixers\Quotes($options),
            new Fixers\MergeTags($options),
            new Fixers\RemoveEmptyTags($options),
            new Fixers\RemoveInnerTags($options),
            new Fixers\CharsInside($options),
            new Fixers\Dash($options)
        );
    }

    public static function log(string $message)
    {
        if ($debug) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    private function __construct(DOMDocument $dom)
    {
        $this->dom = $dom;
    }

    /**
     * Execute a fixer
     */
    public function __invoke(FixerInterface ...$fixers)
    {
        usort($fixers, function (FixerInterface $a, FixerInterface $b) {
            if ($a->getPriority() == $b->getPriority()) {
                return 0;
            }

            return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
        });

        foreach ($fixers as $fixer) {
            $fixer($this);
        }
    }

    /**
     * Returns the fixed string
     */
    public function __toString(): string
    {
        $body = $this->body();
        $html = $this->dom->saveHtml($body);

        //remove <body> and </body>
        return trim(substr($html, 6, -7));
    }

    public function body(): DOMElement
    {
        return $this->dom->getElementsByTagName('body')->item(0);
    }

    public function nodes(int $type)
    {
        $element = $this->body();
        $down = true;

        while (true) {
            if ($element->firstChild && $down) {
                $element = $element->firstChild;

                if ($element->nodeType === $type) {
                    yield $element;
                }
                continue;
            }

            if ($element->nextSibling) {
                $down = true;
                $element = $element->nextSibling;

                if ($element->nodeType === $type) {
                    yield $element;
                }
                continue;
            }

            if ($element->parentNode) {
                $element = $element->parentNode;
                $down = false;
                continue;
            }

            break;
        }
    }

    private static function createDOMDocument(string $content): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->encoding = 'UTF-8';
        $dom->strictErrorChecking = false;
        $dom->substituteEntities = false;
        $dom->preserveWhiteSpace = false;

        $libxmlCurrent = libxml_use_internal_errors(true);
        $mbDetectCurrent = mb_detect_order();
        mb_detect_order('ASCII,UTF-8,ISO-8859-1,windows-1252,iso-8859-15');
        $loaded = $dom->loadHTML(self::fixContentEncoding($content));
        libxml_use_internal_errors($libxmlCurrent);
        mb_detect_order(implode(',', $mbDetectCurrent));

        if (!$loaded) {
            throw new InvalidArgumentException('Cannot load the given HTML via DomDocument');
        }

        return $dom;
    }

    /**
     * Convert the content encoding properly and add Content-Type meta if HTML document.
     *
     * @see http://php.net/manual/en/domdocument.loadhtml.php#91513
     */
    private static function fixContentEncoding(string $content): string
    {
        // Little hack to force UTF-8
        if (strpos($content, '<?xml encoding') === false) {
            $hack = strpos($content, '<body') === false ? '<?xml encoding="UTF-8"><body>' : '<?xml encoding="UTF-8">';
            $content = $hack.$content;
        }

        $encoding = mb_detect_encoding($content);
        $headPos  = mb_strpos($content, '<head>');

        // Add a meta to the <head> section
        if (false !== $headPos) {
            $headPos += 6;
            $content = mb_substr($content, 0, $headPos).
                    '<meta http-equiv="Content-Type" content="text/html; charset='.$encoding.'">'.
                    mb_substr($content, $headPos);
        }

        $content = mb_convert_encoding($content, 'HTML-ENTITIES', $encoding);

        return $content;
    }
}
