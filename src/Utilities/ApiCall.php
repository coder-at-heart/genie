<?php

namespace Lnk7\Genie\Utilities;

use JsonSerializable;
use Lnk7\Genie\Cache;
use Lnk7\Genie\Tools;

/**
 * Simple wrapper for Wordpress wp_remote_post
 *
 * Class API
 * @package Lnk7\Genie
 */
class ApiCall implements JsonSerializable {

    /**
     * url for the API call
     *
     * @var
     */
    var $url;

    /**
     * Method
     *
     * @var string
     */
    var $method = 'POST';

    /**
     * timeout in seconds
     *
     * @var int
     */
    var $timeout = 10;

    /**
     * how many redirections allowed?
     *
     * @var int
     */
    var $redirection = 5;

    /**
     * Which http version to use?
     * @var string
     */
    var $httpVersion = '1.0';

    /**
     * Should this API call block script execution?
     * @var bool
     */
    var $blocking = true;

    /**
     * An array of additional headers to send
     *
     * @var array
     */
    var $headers = [];

    /**
     * API call body
     *
     * @var array
     */
    var $body = [];

    /**
     * Data format
     *
     * @var string
     */
    var $data_format = 'query';

    /**
     * An Array of cookies to send with the request
     *
     * @var array
     */
    var $cookies = [];

    /**
     * The response
     *
     * @var array
     */
    var $response = [];

    /**
     * Should this API call be cached?
     *
     * @var bool
     */
    var $cache = false;

    /**
     * How long should the data be cached for?
     *
     * @var int
     */
    var $cacheFor = 300;



    /**
     * HttpClient constructor.
     *
     * @param $url
     */
    public function __construct( $url ) {

        $this->url = $url;

        return $this;
    }



    /**
     * Get shortcut
     *
     * @param $url
     *
     * @return bool|mixed
     */
    public static function get( $url, $vars = [] ) {

        $call = static::to( $url )
                      ->usingMethod( 'GET' )
                      ->withBody( $vars )
                      ->send();
        if ( $call->wasSuccessful() ) {
            return $call->getResponseBody();
        } else {
            return false;
        }
    }



    /**
     * Do the API call
     *
     * @return $this
     */
    public function send() {

        // Build a Transient Key
        $key = Cache::getCachePrefix() . '_api_' . md5( serialize( [
                'url'         => $this->url,
                'method'      => $this->method,
                'timeout'     => $this->timeout,
                'redirection' => $this->redirection,
                'httpversion' => $this->httpVersion,
                'blocking'    => $this->blocking,
                'headers'     => $this->headers,
                'body'        => $this->body,
                'cookies'     => $this->cookies,
                'data_format' => $this->data_format,
            ] ) );

        if ( false === ( $this->response = get_transient( $key ) ) or ! $this->cache ) {

            // It wasn't there, so regenerate the data and save the transient
            $this->response = wp_remote_post( $this->url, [
                    'method'      => $this->method,
                    'timeout'     => $this->timeout,
                    'redirection' => $this->redirection,
                    'httpversion' => $this->httpVersion,
                    'blocking'    => $this->blocking,
                    'headers'     => $this->headers,
                    'body'        => $this->body,
                    'cookies'     => $this->cookies,
                    'data_format' => $this->data_format,
                ]
            );
            if ( $this->cache ) {
                set_transient( $key, $this->response, $this->cacheFor );
            }
        }

        return $this;

    }



    /**
     * Add the Body for the API call
     *
     * @param $body
     *
     * @return $this
     */
    public function withBody( $body ) {

        $this->body = $body;

        return $this;
    }



    /**
     * Set the Method of this call
     *
     * @param $method
     *
     * @return $this
     */
    public function usingMethod( $method ) {

        $this->method = $method;

        return $this;
    }



    /**
     * Nice way to initialise
     *
     * $call = API::to('https://www.somedomain.com')
     *   ->body(['a'=>1,'b'=>2 ])
     *   ->send();
     *
     * if ($call->wasSuccessful() {
     * }
     *
     * @param $url
     *
     * @return ApiCall
     */
    public static function to( $url ) {

        return new static( $url );
    }



    /**
     * Returns true if the APi called was Successful
     *
     * @return bool
     */
    public function wasSuccessful() {

        return ! static::failed();
    }



    /**
     * Gets the response body
     *
     * @return bool|mixed
     */
    public function getResponseBody() {

        if (!isset( $this->response['body'] ) ) {
            return false;
        }
        return Tools::maybeConvertFromJson( $this->response['body']);

    }



    /**
     * Simple post shortcut
     *
     * @param $url
     * @param $vars
     *
     * @return bool|mixed
     */

    public static function post( $url, $vars ) {

        $call = static::to( $url )
                      ->withBody( $vars )
                      ->send();
        if ( $call->wasSuccessful() ) {
            return $call->getResponseBody();
        } else {
            return false;
        }
    }



    /**
     * Add Multiple Headers
     *
     * @param $headers
     *
     * @return $this
     */
    public function addHeaders( $headers ) {

        foreach ( $headers as $header => $data ) {
            $this->addHeader( $header, $data );
        }

        return $this;
    }



    /**
     * Add a header to the API call
     *
     * @param $header
     * @param $data
     *
     * @return $this
     */
    public function addHeader( $header, $data ) {

        $this->headers[ $header ] = $data;

        return $this;
    }



    /**
     * Add a json Body. cannot be used at the same time as withBody()
     *
     * @param $json
     *
     * @return $this
     */
    public function withJson( $json ) {

        $this->withBody(Tools::maybeConvertToJson($json) );
        $this->setDataFormat( 'body' );
        $this->addHeader( 'Content-Type', 'application/json; charset=utf-8' );

        return $this;
    }



    /**
     * Set the data Format
     *
     * @param $format
     *
     * @return $this
     */
    public function setDataFormat( $format ) {

        $this->data_format = $format;

        return $this;
    }



    /**
     * Cache Results
     *
     * @param int $seconds
     *
     * @return $this
     */
    public function cacheFor( int $seconds ) {

        $this->enableCache( $seconds );

        return $this;

    }



    /**
     * Cache Results
     *
     * @param int $seconds
     *
     * @return $this
     */
    public function enableCache( int $seconds = 3600 * 12 ) {

        $this->cache    = true;
        $this->cacheFor = $seconds;

        return $this;

    }



    /**
     * Disable the Cache
     *
     * @return $this
     */
    public function disableCache() {

        $this->cache = false;

        return $this;

    }



    /**
     * Returns true if the APi called failed
     *
     * @return bool
     */
    public function failed() {

        $code = $this->response['response']['code'];
        return is_wp_error( $this->response )  or $code < 200 or $code > 299 ;
    }



    /**
     * Returns the full response
     *
     * @return array
     */
    public function getResponse() {

        return $this->response;
    }



    /**
     * Get response headers
     *
     * @return bool|mixed
     */
    public function getResponseHeaders() {

        return isset( $this->response['headers'] ) ? $this->response['headers'] : false;
    }



    public function jsonSerialize() {
        return [
            'success' => $this->wasSuccessful(),
            'code'    => $this->getResponseCode(),
            'body'    => $this->getResponseBody(),
        ];
    }


    /**
     * Get Response Code
     *
     * @return bool
     */
    public function getResponseCode() {

        return isset( $this->response['response'] ) ? $this->response['response']['code'] : false;
    }



    /**
     * get response Message
     *
     * @return bool
     */
    public function getResponseMessage() {

        return isset( $this->response['response'] ) ? $this->response['response']['message'] : false;
    }

}