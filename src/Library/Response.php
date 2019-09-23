<?php

namespace Lnk7\Genie\Library;

class Response {

    private $responseCode = 200;

    private $success = true;

    private $message;

    private $data;



    public static function Success( $message, $data = null ) {
        $response = new static( 200 );
        $response->withData( $data )
                 ->withMessage( $message )
                 ->wasSuccessful()
                 ->send();

    }



    public static function Failure( $message, $data = null ) {
        $response = new static( 200 );
        $response->failed()
                 ->withMessage( $message )
                 ->withData( $data )
                 ->send();
    }



    public static function NotFound( $message, $data = null ) {
        $response = new static( 404 );
        $response->failed()
                 ->withMessage( $message )
                 ->withData( $data )
                 ->send();
    }



    function __construct( int $responseCode = null ) {
        if ( ! is_null( $responseCode ) ) {
            $this->responseCode = $responseCode;
        }
    }



    function wasSuccessful() {
        $this->success = true;

        return $this;
    }



    function failed() {
        $this->success = false;

        return $this;
    }



    function withMessage( string $message ) {
        $this->message = $message;

        return $this;
    }



    function withData( $data ) {
        $this->data = $data;

        return $this;
    }



    function send() {

        $responseData = [
            'success' => $this->success
        ];

        if ( ! is_null( $this->message ) ) {
            $responseData['message'] = $this->message;
        }

        if ( ! is_null( $this->data ) ) {
            $responseData['response'] = $this->data;
        }
        header( 'Content-Type: application/json', true, $this->responseCode );

        echo json_encode( $responseData );
        exit;
    }

}