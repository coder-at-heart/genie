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
    protected $responseCode;


    /**
     * Response constructor.
     *
     * @param int $responseCode
     */
    function __construct(int $responseCode = 200)
    {
        $this->responseCode = $responseCode;
    }


    /**
     * @param mixed $data
     * Send a Success response
     */
    public static function success($data = [])
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

        http_response_code($this->responseCode);
        header('Content-Type: application/json');
        echo json_encode($this->data);
        exit;
    }


    /**
     * Specify the data to return
     *
     * @param mixed $data
     *
     * @return $this
     */
    function withData($data)
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Send a failure response
     *
     * @param mixed $data
     */
    public static function failure($data = [])
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
    public static function error($data = [])
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