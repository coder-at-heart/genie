<?php

namespace Lnk7\Genie\Abstracts;

use ReflectionClass;

abstract class Template {

    protected static function getTemplate() {
        $class    = new ReflectionClass( static::class );
        $filename = str_replace( '\\', '/', $class->getFileName() );

        return substr( $filename, strlen( get_stylesheet_directory() ) + 1 );
    }

}