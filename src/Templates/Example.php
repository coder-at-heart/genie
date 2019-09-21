<?php

namespace Lnk7\Genie\Templates;

use Lnk7\Genie\Abstracts\Template;
use Lnk7\Genie\Fields\TabField;
use Lnk7\Genie\Fields\TextField;
use Lnk7\Genie\Fields\TrueFalseField;
use Lnk7\Genie\Utilities\CreateSchema;
use Lnk7\Genie\Utilities\Where;

class Example extends Template {

    protected static $view = 'Templates\example.twig';

    protected static $template = 'templates\example.php';



    public static function Setup() {

        CreateSchema::Called( 'Example Template' )
                    ->withFields( [
                        TabField::Called( 'Settings' ),
                        TrueFalseField::Called( 'show_text' )
                                      ->message( 'Should we show the text?' )
                                      ->wrapperWidth( 25 ),
                        TextField::Called( 'text' ),
                    ] )
                    ->shown( Where::field( 'post_template' )->equals( static::getTemplate() ) )
                    ->register();

    }



    protected static function process() {

        //Do you logic & processing
        static::addVar( 'name', 'Sunil' );

    }

}