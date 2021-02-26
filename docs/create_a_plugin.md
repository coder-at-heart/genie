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
