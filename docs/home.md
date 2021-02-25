# Genie: The Wordpress Programmer's Toolkit

Quick overview

- ACF Pro abstraction with `CreateSchema`
- Twig 2.0 support with `View`
- Custom Post Type Abstraction `CustomPost`
- add_action class `OnAction`
- add_filter class `OnFilter`
- `CreateCustomPostType` Utility
- `CreateTaxonomy` Utility
- Wordpress wp-mail Wrapper `SendEmail`
- Wordpress API wrapper `ApiCall`
- Background Job Processing `BackgrounJob`
- Handy Ajax Controller `Ajax`

## Installation

`composer require "lnk7\genie"`

## Custom Posts

```php
<?php

namespace MyPlugin\PostTypes;

use Lnk7\Genie\Abstracts\CustomPost;
use Lnk7\Genie\Utilities\CreateCustomPostType;
use Lnk7\Genie\Utilities\CreateTaxonomy;

class FAQ extends CustomPost
{

    static $postType = 'faq';

    static $taxonomy = 'faq_category';

    static public function setup()
    {

        CreateTaxonomy::called(static::$taxonomy)
            ->register();

        CreateCustomPostType::called(static::$postType)
            ->icon('dashicons-admin-comments')
            ->addTaxonomy(static::$taxonomy)
            ->backendOnly()
            ->removeSupportFor(['thumbnail'])
            ->register();

    }

}
```

You can now

```php
<?php

$faqs = FAQ::get();

foreach($faqs as $faq) { 

  echo $faq->post_title;
  echo $faq->post_content;

}

$first = FAQ::get()->first();

$faq = FAQ::create([
    'post_title' => 'A question',
    'post_content' => 'The Answer'
]);

``` 
