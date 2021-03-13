---
layout: page
title: Views
permalink: /views
nav_order: 3
---
# Genie Views 
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}
   
## Genie uses Twig 
Genie uses twig behind the scenes to ensure separation of concerns. By 
default Genie looks for veiws in the `src/views` folder. 


## Example Component using a View

```php
<?php

namespace GeniePluginExample;

use Lnk7\Genie\Interfaces\GenieComponent;
use Lnk7\Genie\Utilities\HookInto;
use Lnk7\Genie\View;

class Footer implements GenieComponent
{

    public static function setup()
    {
       HookInto::action('wp_footer')
         ->run(function() {
             View::with('footer.twig')
              ->display();
       });
    }
}
```
Genie will look into the `src/twig` folder for `footer.twig` 

`footer.twig`

```html
<p>This is a twig file!</p>
```
## Passing Variables to templates

### Individual variables

```php
use Lnk7\Genie\View;

View::with('footer.twig')
  ->addVar('user', get_current_user())
  ->display();
```

### As an array

```php
use Lnk7\Genie\View;

View::with('footer.twig')
->addVars([
  'time' => time(),
  'user' => get_current_user()
])
->display();
```

## Returning html

use `render()` to return html

```php
use Lnk7\Genie\View;

$html = View::with('footer.twig')
->addVars([
  'name' => 'sunil',
  'user' => get_current_user()
])
->render();
```

## Changing the default path for views

When creating a genie plugin or theme you can specify the location of twig 
files using the `useViewsFrom()` method.

```php
use Lnk7\Genie\Genie;

Genie::createPlugin()
    ->withComponents([
    //component classes
    ])
    ->useViewsFrom( 'my_dir/my_folder')
    ->start();
```
