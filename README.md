# Typofixer

A [wip] PHP library to fix microtypography issues in html code.

## Requirements

* PHP ^7.0
* mbstring extension

## Usage

```php
use Typofixer\Fixer;

$input = '<p>"Hello"&nbsp;&nbsp;world...</p>';
$output = Fixer::fix($input);

echo $ouput; //<p>“Hello” world…</p>
```

## Available fixers:

Name | Description
-----|-------------
**Ellipsis** | Converts `...` into `…`
**MergeTags** | Merge two consecutive tags. Ex: `<b>hello</b> <b>world</b>` is converted to `<b>hello world</b>`
**NoSpaceBefore** | Removes the space before some characteres like `,:;!?`. Ex: `hello , world` is converted to `hello, world`.
**Quotes** | Replace plain quotes by curly quotes. Ex: `"hello word"` is converted to `“hello world”`
**RemoveEmptyTags** | Removes empty tags or tags containing only spaces. Ex: `<strong> </strong>`
**RemoveInnerTags** | Removes some tags that cannot be inside other tags. Ex: `<strong>hello <strong>world</strong></strong>` is converted to `<strong>hello world</strong>`
**Spaces** | Normalize spaces, removes `&nbsp;` and other fixes. Ex: `Hello &nbsp; world` is converted to `Hello world`.
