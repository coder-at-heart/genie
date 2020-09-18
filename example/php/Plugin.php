<?php

namespace GeniePluginExample;

use GeniePluginExample\Exceptions\GeniePluginExampleException;
use Lnk7\Genie\Interfaces\GenieComponent;
use Lnk7\Genie\Utilities\HookInto;
use Lnk7\Genie\Utilities\RegisterApi;

class Plugin implements GenieComponent
{


    public static function setup()
    {
        /**
         * Make sure we load jQuery
         */
        HookInto::action('init')
            ->run(function () {
                wp_enqueue_script('jQuery');
            });

        RegisterApi::get('test')->run([static::class, 'testException']);
    }


    /**
     * @throws GeniePluginExampleException
     */
    public static function testException()
    {
        throw  GeniePluginExampleException::withMessage('test')
            ->withCode(102)
            ->withData('hello');

    }

}