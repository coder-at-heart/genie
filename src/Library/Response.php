<?php

namespace Lnk7\Genie\Library;

class Response {

    private $responseCode = 200;

    private $data;


    public static function Success( $data ) {
        $response = new static( 200 );
        $response->withData( $data )
                 ->send();
    }

    public static function Failure( $data ) {
        $response = new static( 400 );
        $response->withData( $data )
                 ->send();
    }


    public static function NotFound( $data) {
        $response = new static( 404 );
        $response->withData( $data )
                 ->send();
    }



    function __construct( int $responseCode = null ) {
        if ( ! is_null( $responseCode ) ) {
            $this->responseCode = $responseCode;
        }
    }

    function withData( $data ) {
        $this->data = $data;

        return $this;
    }



    function send() {

        header( 'Content-Type: application/json', true, $this->responseCode );

        echo json_encode( $this->data );
        exit;
    }

}