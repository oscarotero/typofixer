<?php
declare(strict_types=1);

namespace Typofixer;

use DOMDocument;
use InvalidArgumentException;
use Typofixer\Fixers\FixerInterface;

class Fixer
{
    private $dom;

    public static function fix(string $content, array $fixers = []): string
    {
        $self = self::create($content);

        if (empty($fixers)) {
            $fixers = [
                new Fixers\Spaces(),
                new Fixers\SpaceAfter(),
                new Fixers\Ellipsis(),
                new Fixers\NoSpaceBefore(),
                new Fixers\SpaceTags(),
                new Fixers\Quotes(),
                new Fixers\MergeTags(),
                new Fixers\RemoveEmptyTags(),
                new Fixers\RemoveInnerTags(),
            ];
        }

        foreach ($fixers as $fixer) {
            $self($fixer);
        }

        return (string) $self;
    }

    public static function create(string $content): self
    {
        $dom = self::createDOMDocument($content);
        return new static($dom);
    }

    public function __construct(DOMDocument $dom)
    {
        $this->dom = $dom;
    }

    /**
     * Execute a fixer
     *
     * @param FixerInterface $fixer
     */
    public function __invoke(FixerInterface $fixer)
    {
        $fixer($this);
    }

    /**
     * Returns the fixed string
     *
     * @return string
     */
    public function __toString(): string
    {
        $body = $this->dom->getElementsByTagName('body')->item(0);
        $html = $this->dom->saveHtml($body);

        //remove <body> and </body>
        return trim(substr($html, 6, -7));
    }

    public function nodes(int $type)
    {
        $element = $this->dom->getElementsByTagName('body')->item(0);
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

    /**
     * @param string $content
     *
     * @return DOMDocument
     */
    private static function createDOMDocument($content): DOMDocument
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
     *
     * @param string $content
     *
     * @return string
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
