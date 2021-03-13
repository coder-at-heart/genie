---
layout: page title: Hooks & Filters permalink: /reference/hooks-and-filters
parent: Reference
---

# Hooks & Filters

{: .no_toc }

## Table of contents

{: .no_toc .text-delta }

1. TOC {:toc}

## Main Usage

You can use the `HookInto` utility to hook into actions and filters. Genie uses
Reflection to work out how many parameters are being use in the closure or
function

## Basic usage

```php
use Lnk7\Genie\Utilities\HookInto;
HookInto::action('init')
  ->run(function() {
   // run something here
  });
```

## Setting Priority

The default priority is 10.

```php
use Lnk7\Genie\Utilities\HookInto;
HookInto::action('init', 20)
  ->run(function() {
   // run something here
  });
```

## Using a callback

any callable is accepted by the `run()` method.

```php
use Lnk7\Genie\Utilities\HookInto;
HookInto::action('init', 20)
  ->run( [\Lnk7\Genie\Debug::class, 'dd']);
```

## Multiple Hooks

You can add multiple hooks or filters at the same time using `orAction()`  
or for filters `orFilter()`

```php
use Lnk7\Genie\Utilities\HookInto;
HookInto::action('init', 20)
  ->orAction('wp_loaded')
  ->run( function() { 
    // run something
  });
```

## Filters

rather than use action - you can use the `filter()` method:

```php
use Lnk7\Genie\Utilities\HookInto;
HookInto::filter('the_content')
    ->run( function($content) { 
      // run something
      return $content;
  });
```
