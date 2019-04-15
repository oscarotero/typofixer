# Typofixer

A [wip] PHP library to fix microtypography issues in html code.

## Requirements

* PHP ^7.1
* mbstring extension

## Usage

```php
use Typofixer\Typofixer;

$input = '<p>"Hello"&nbsp;&nbsp;world...</p>';
$output = Typofixer::fix($input, ['language' => 'es']);

echo $ouput; //<p>«Hello» world…</p>
```

## Available fixers:

Name | Description
-----|-------------
**AddSpaceAfter** | Ensure there's a space after some characters like `,:;!?`. Ex: `hello,world` is converted to `hello, world`.
**CharsInside** | Move some characters inside the corresponding tags. Ex: `<strong>hello</strong>, world` is converted to `<strong>hello,</strong> world`
**Dash** | Replace the simple `-` between numbers to ndash and `--` to mdash.
**Ellipsis** | Converts `...` into `…`
**MergeTags** | Merge two consecutive tags. Ex: `<b>hello</b> <b>world</b>` is converted to `<b>hello world</b>`
**Quotes** | Replace plain quotes by curly quotes. Ex: `"hello word"` is converted to `“hello world”`
**RemoveEmptyTags** | Removes empty tags or tags containing only spaces. Ex: `<strong> </strong>`
**RemoveInnerTags** | Removes some tags that cannot be inside other tags. Ex: `<strong>hello <strong>world</strong></strong>` is converted to `<strong>hello world</strong>`
**RemoveSpaceBefore** | Removes the space before some characteres like `,:;!?`. Ex: `hello , world` is converted to `hello, world`.
**RemoveSpaceAfter** | Removes the space after some characteres like `¿¡(`. Ex: `hello ( world)` is converted to `hello (world)`.
**Spaces** | Removes duplicated spaces and convert all unicode spaces (like `&nbsp;`) to simple spaces. Ex: `Hello &nbsp; world` is converted to `Hello world`.
**SpaceTags** | Normalize spaces between tags. Ex: `<strong>hello </strong>world` is converted to `<strong>hello</strong> world`.

## Available options

Name | Description
-----|-------------
`language` | The ISO language code that is used by some fixers like Quotes