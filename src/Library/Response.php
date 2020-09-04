<?php

namespace Lnk7\Genie\Library;

class Response
{

    /**
     * The default http response code
     *
     * @var int
     */
    private $responseCode = 200;

    /**
     * Data to return back to the client
     *
     * @var array $data
     */
    private $data;



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
     * @param $data
     * Send a Success response
     */
    public static function Success($data)
    {
        $response = new static(200);
        $response->withData($data)
            ->send();
    }



    /**
     * Send the response back to the browser.
     */
    function send()
    {

        header('Content-Type: application/json', true, $this->responseCode);

        echo json_encode($this->data);
        exit;
    }



    /**
     * Specify the data to return
     *
     * @param $data
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
     * @param $data
     */
    public static function Failure($data)
    {
        $response = new static(400);
        $response->withData($data)
            ->send();
    }




    /**
     * Send an error response
     *
     * @param $data
     */
    public static function Error($data)
    {
        $response = new static(500);
        $response->withData($data)
            ->send();
    }



    /**
     * Send a Not found response
     *
     * @param $data
     */
    public static function NotFound($data)
    {
        $response = new static(404);
        $response->withData($data)
            ->send();
    }

}