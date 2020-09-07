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
        HookInto::action('init')
            ->run(function () {
                wp_enqueue_script('jQuery');
            });
    }

}