<?php

namespace GeniePluginExample;

use GeniePluginExample\Exceptions\GeniePluginExampleException;
use Lnk7\Genie\Debug;
use Lnk7\Genie\Interfaces\GenieComponent;
use Lnk7\Genie\Response;
use Lnk7\Genie\Utilities\AddShortcode;
use Lnk7\Genie\Utilities\HookInto;
use Lnk7\Genie\Utilities\RegisterApi;
use Lnk7\Genie\View;

class Shortcodes implements GenieComponent
{


    public static function setup()
    {
        AddShortcode::called('test_twig')
            ->run(function(){

                $valid = View::isValidTwig('{{hello}} Tosh');

                return $valid ?  'is valid Twig Code' : 'is Invalid twig code';

            });
    }


}