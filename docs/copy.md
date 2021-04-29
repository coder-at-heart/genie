---
layout: page 
title: Genie Press 
nav_order: 1
---

# GeniePress: The Framework for WordPress Developers 

GeniePress is a plugin and theme framework with expressive elegant syntax. 
Should we say - It's reinventing poetry?

It makes plugin and theme development fun, while never having to mix html 
and php again. 

Combined with Vue and Tailwind, you have a new foundation for creating 
powerful themes and plugins.

## Add Genie through composer

`composer require "lnk7\genie"`

## Update composer.json

In this example we'll be using the namespace `GeniePluginExample`, so let's
update `composer.json` with the right location for the php files. In this
example We're storing php files in the `src/php` folder.

```json
{
  "name": "lnk7/test",
  "description": "Testimonial Plugin using GeniePress",
  "license": "GPL-2.0-or-later",
  "require": {
    "lnk7/genie": "^1"
  },
  "minimum-stability": "stable",
  "autoload": {
    "psr-4": {
      "GeniePluginExample\\": "src/php"
    }
  }
}
```

## Create your plugin file

Now let's create our plugin file. We'll call it `testimonials.php`

```php
<?php

/**
 * Plugin Name:       Testimonials
 * Description:       Let's see how Genie performs
 * Version:           1.0.0
 */

namespace GeniePluginExample;

use Plugin\PostTypes\Testimonial
;use Lnk7\Genie\Genie;

// Include composer
include_once('vendor/autoload.php');

// Get Genie Going  - Fire in the Genie Components
Genie::createPlugin()
    ->withComponents([
            Plugin::class,
            Testimonial::class,
    ])
    ->start();
```

`Genie` has two main methods called `createPlugin()` and `createTheme()`.  
If you are creating a theme, all the steps in the tutorial are the same except
the above code would be in your `functions.php` file.

The above code tells Genie to load the `Plugin` class.

I like encapsulating code in components

## The `Plugin` component

Each Component implements the `GenieComponent` Interface - It must have a
`setup()` method.

In this example, the Plugin class will be responsible for ensuring jQuery is
loaded in the front-end.

```php
<?php

namespace GeniePluginExample;

use Lnk7\Genie\Interfaces\GenieComponent;
use Lnk7\Genie\Utilities\HookInto;

class Plugin implements GenieComponent
{

    public static function setup()
    {
        /**
         * Make sure we load jQuery
         */
        HookInto::action('wp_enqueue_scripts')
            ->run(function () {
                wp_enqueue_script('jQuery');
            });

    }

}
```

Here we're using Genie's `HookInto` utility to hook into the
`wp_enqueue_scripts` to ensure jQuery is loaded.  `HookInto` uses reflection to
work out the parameters required to be sent into the function (here there are
none).

The `setup()` method is the place to add all hooks, register schemas and APIs

## Custom Posts and ACF

So let's have a look at the testimonial component

Genie provides a handy utility to register CustomPosts. Your Class should
extend `CustomPost`

Here is a sample custom Post type called Testimonial

```php
<?php

namespace Plugin\PostTypes;

use Lnk7\Genie\Abstracts\CustomPost;
use Lnk7\Genie\Fields\SelectField;
use Lnk7\Genie\Fields\TextField;
use Lnk7\Genie\Utilities\AddShortcode;
use Lnk7\Genie\Utilities\CreateCustomPostType;
use Lnk7\Genie\Utilities\CreateSchema;
use Lnk7\Genie\Utilities\RegisterAjax;
use Lnk7\Genie\Utilities\RegisterApi;
use Lnk7\Genie\Utilities\Where;
use Lnk7\Genie\View;

/**
 * Class Testimonial
 *
 * @package GeniePlugin\PostTypes
 * 
 * I'm adding these here my IDE knows these exist 
 * @property string $name
 * @property string $location
 */
class Testimonial extends CustomPost
{

    // This is the post type
    static $postType = 'testimonial';


    // We're going to use this later in the ACF Schema Definition, and in 
    // our ajax validation callback
    protected static $locations = [
        'gb' => 'London',
        'fr' => 'France',
    ];


    // This is called by Genie  
    public static function setup()
    {
    
        // We should call the parent setup();
        parent::setup();

        // Use a Genie to register the custom post. Most classes
        // in genie are fluent.  I just find it reads better
        CreateCustomPostType::called(static::$postType)
            ->icon('dashicons-admin-comments')
            ->register();


        // Create an ACF Schema ! 
        CreateSchema::called('Testimonial')
            ->withFields([
                // We have a whole bunch of fields we can use - Check teh 
                // Fields folder
                TextField::called('name')
                    ->required(true)
                    ->wrapperWidth(50),
                SelectField::called('location')
                    ->choices(static::$locations)
                    ->default('london')
                    ->returnFormat('value')
                    ->required(true)
                    ->wrapperWidth(50),

            ])
            // Where should this be shown?
            ->shown(Where::field('post_type')->equals(static::$postType))
            //Attach it to this class so we can do fancy stuff - see later
            ->attachTo(static::class)
            ->register();

        // Use genie to register an ajax endpoint
        RegisterAjax::url('testimonial/create')
            ->run([static::class, 'addTestimonial']);

        // Register an api endpoint to return all the Testimonials
        RegisterApi::get('testimonials')
            ->run(function() {
                return static::get()->toArray();
            });


        // Let's setup the testimonial form 
        AddShortcode::called('testimonial_form')
            ->run(function () {
                // nice , we're using twig !   All your 
                return View::with('testimonials/form.twig')
                    ->addVar('locations', static::$locations)
                    ->render();
            });

        // Show one Testimonial
        AddShortcode::called('testimonial')
            ->run(function ($attributes) {
                $attributes = (object)shortcode_atts([
                    'name' => $attributes[0],
                ], $attributes);

                $testimonial = static::get([
                    'meta_key'   => 'name',
                    'meta_value' => $attributes->name,
                ])->first();

                return View::with('testimonials/testimonial.twig')
                    ->addVar('testimonial', $testimonial)
                    ->addVar('locations', static::$locations)
                    ->render();
            });


        // Show all testimonials
        AddShortcode::called('testimonials')
            ->run(function ($attributes) {
                $testimonials = static::get();

                return View::with('testimonials/testimonials.twig')
                    ->addVar('testimonials', $testimonials)
                    ->addVar('locations', static::$locations)
                    ->render();
            });
    }


    /**
     * Add a testimonial - called from Ajax
     *
     * @param $title
     * @param $text
     * @param $name
     * @param $location
     *
     * @return array
     */
    public static function addTestimonial($title, $text, $name, $location)
    {
    
        // There's not much we need to do here, Genie has already made sure 
        // that each of the parameters being sent by the ajax call have been 
        // set.
    
    
        // let's create the testimonial.
        $testimonial = static::create([
            'post_title'   => $title,
            'post_content' => $text,
            'name'         => $name,
            'location'     => $location,
            'post_status'  => 'draft',
        ]);

        // Genie will convert this back to json for us
        return [
            'message' => 'Testimonial pending approval',
            'id'      => $testimonial->ID,
        ];
    }

}
```

## Let's have a look at the twig files

By default Genie looks in the 'src/twig' folder for your twig files

### Form.twig

```html
{% raw %}
<form id="testimonialForm">
    <p><label for="title">Title</label><input type="text" id="title" required>
    </p>
    <p><label for="testimonial">Testimonial</label><textarea
            id="testimonial"></textarea></p>
    <p><label for="name">Name</label><input type="text" id="name" required></p>
    <p><label for="location">Location</label>
        <select id="location" required>
            {% for code,text in locations %}
            <option value="{{ code }}">{{ text }}</option>
            {% endfor %}
        </select>
    </p>

    <button type="submit" id="submitButton">Save</button>
    <p><span id="message"></span></p>
</form>

<script>

    jQuery(document).ready(function ($) {

        var buttonClicked = false;

        $('#testimonialForm').submit(function (event) {
            event.preventDefault();

            if (buttonClicked) {
                return;
            }

            buttonClicked = true;

            $.ajax({

                // Checkout the sneaky ajax_url twig function that grabs the 
                // right url and add the needed nonce.  
                url: '{{ ajax_url('testimonial / create
            ') }}',
                    data
        :
            JSON.stringify({
                title: $('#title').val(),
                text: $('#testimonial').val(),
                name: $('#name').val(),
                location: $('#location').find(":selected").val()
            }),
                    type
        :
            'POST',
                    dataType
        :
            'json',
                    contentType
        :
            'application/json',
                    complete
        :

            function (data) {
                buttonClicked = false
                if (data.status === 200) {
                    $('#testimonialForm')[0].reset();
                    $('#message').html(data.responseJSON.message)
                }
            }
        })
            ;
        })
    });
</script>
{% endraw %}
```

## testimonial.twig

```html
{% raw %}
<blockquote>

    <img src="{{ testimonial.featuredImage('thumbnail').url }}"/>
    <h3{{ testimonial.post_title }}</p>
    <p>{{ testimonial.post_content|wpautop }}</p>
    <footer>—{{ testimonial.name }}, <cite>{{ locations[testimonial.location]
        }}</cite></footer>
</blockquote>
{% endraw %}
```

## testimonials.twig

We love twig - code reuse :)

```html
{% raw %}
{% for testimonial in testimonials %}
{% include 'testimonials/testimonial.twig' %}
{% endfor %}
{% endraw %}
```



