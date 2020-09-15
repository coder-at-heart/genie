<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Traits\HasData;

class Response
{


    use HasData;

    /**
     * The default http response code
     *
     * @var int
     */
    protected $responseCode = 200;


    /**
     * Response constructor.
     *
     * @param int|null $responseCode
     */
    function __construct(int $responseCode = null)
    {
        if (!is_null($responseCode)) {
            $this->responseCode = $responseCode;
        }
    }


    /**
     * @param mixed $data
     * Send a Success response
     */
    public static function success( $data = [])
    {
        $response = new static(200);
        $response->withData($data)
            ->send();
    }


    /**
     * Send the response back to the browser.
     */
    public function send()
    {
        header('Content-Type: application/json', true, $this->responseCode);

        echo json_encode($this->data);
        wp_die();
    }


    /**
     * Specify the data to return
     *
     * @param mixed $data
     *
     * @return $this
     */
    function withData( $data)
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Send a failure response
     *
     * @param mixed $data
     */
    public static function failure( $data = [])
    {
        $response = new static(400);
        $response->withData($data)
            ->send();
    }


    /**
     * Send an error response
     *
     * @param mixed $data
     */
    public static function error( $data = [])
    {
        $response = new static(500);
        $response->withData($data)
            ->send();
    }


    /**
     * Send a not found response
     *
     * @param mixed $data
     */
    public static function notFound($data)
    {
        $response = new static(404);
        $response->withData($data)
            ->send();
    }

}