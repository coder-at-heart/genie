---
layout: page 
title: Shortcodes 
parent: Reference
---

# Shortcodes
{: .no_toc }
<details open markdown="block">
  <summary>
    Table of contents
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

## Main Usage

You can use the `HookInto` utility to hook into actions and filters. Genie uses
Reflection to work out how many parameters are being use in the closure or
function

## Basic usage

```php
use Lnk7\Genie\Utilities\AddShortcode;
AddShortcode::called('test_me')
  ->run(function($attributes,$content) {
    // process your shortcode here
});
```

## Using Views with Shortcodes

```php
use Lnk7\Genie\Utilities\AddShortcode;
AddShortcode::called('user')
  ->run(function($attributes,$content) {
     
    return \Lnk7\Genie\View::with('shortcodes/user.twig')
      ->addVars([
         'user' => wp_get_current_user(),
         'attributes' => $attributes,
      ])
      ->render();
});
```

The shortcode could be user :

```html
Hello [user field=name ],
```

And the `shortcodes/user.twig` file

```twig
{% raw %}
{% if attributes.field == 'name' and user.first_name %}
  {{user.first_name}}
{% else %}
  there
{% endif %}
{% endraw %}
```

Learn more about using Views
