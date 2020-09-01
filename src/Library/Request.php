<?php

namespace Lnk7\Genie\Library;

use Lnk7\Genie\Exception\GenieException;
use Lnk7\Genie\Tools;

class Request {

    /**
     * Input variables
     *
     * @var null
     */
    private static $data = null;



    /**
     * Get a value from the input
     *
     * @param $var
     *
     * @return bool|mixed
     * @throws GenieException
     */
    public static function get( $var ) {
        static::maybeParseInput();
        if ( isset( static::$data[ $var ] ) ) {
            return static::$data[ $var ];
        }

        return false;

    }



    /**
     * Collect Data from various input mechanisms
     *
     * @throws GenieException
     */
    private static function maybeParseInput() {

        if ( ! is_null( static::$data ) ) {
            return;
        }

        static::$data = [];

        $body = file_get_contents( 'php://input' );

        if ( $body ) {
            static::$data = array_merge( static::$data, json_decode( $body, true ) );
            if ( json_last_error() !== JSON_ERROR_NONE ) {
                throw new GenieException( 'Invalid json received' );
            }
        }

        if ( ! empty( $_GET ) ) {
            static::$data = array_merge( static::$data, Tools::stripSlashesArray( $_GET ) );
        }

        if ( ! empty( $_POST ) ) {
            static::$data = array_merge( static::$data, Tools::stripSlashesArray( $_POST ) );
        }

    }

}