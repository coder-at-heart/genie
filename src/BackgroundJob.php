<?php

namespace Lnk7\Genie;

/**
 * Class BackgroundJob
 * Examples.
 * $calls = BackgroundJob::start();
 * $calls->add( some::class . '::method', $param );
 * $calls->send();
 * BackgroundJob::start()
 *  ->add( Object::class . '::method', [ 'param1' => $x, 'param2' => $y ] )
 *  ->send();
 */
class BackgroundJob
{

    /**
     * The variable used to pass the post ID
     *
     * @var string
     */
    static $getVariable = 'genie_bj_id';

    /**
     * The job we're currently processing
     * Once set this turns off all other triggers.
     *
     * @var int
     */
    static $processingId = false;

    /**
     * Array of function calls to perform on this background Job.
     *
     * @var array
     */
    var $calls = [];



    /**
     * Wordpress Hooks !
     */
    public static function Setup()
    {

        //  Check if we're processing a background Job
        if (isset($_GET[static::$getVariable]) and $_GET[static::$getVariable]) {
            $id = absint($_GET[static::$getVariable]);

            // This clever bit of code ends the connection so we don't hold up the user.
            ob_end_clean();
            ignore_user_abort(true);
            ob_start();
            header("Connection: close");
            header("Content-Length: " . ob_get_length());
            ob_end_flush();
            flush();

            // Stash the id we need to process in the init hook
            static::$processingId = $id;
            add_action('init', static::class . '::init', 10000);

        }

    }



    /**
     * Hook into Wordpress Init to Run any jobs.
     * This will usually only happen from a call from curl.
     */
    public static function init()
    {

        // Do we have a job to process
        if (static::$processingId) {
            static::ProcessBackGroundJob(static::$processingId);
            wp_die();
        }
    }



    /**
     * process this Job !
     *
     * @param $id
     */
    public static function ProcessBackGroundJob($id)
    {

        set_time_limit(0);

        $job = get_post($id);

        $calls = unserialize(base64_decode($job->post_content));
        foreach ($calls as $args) {
            $callback = array_shift($args);
            call_user_func_array($callback, $args);
        }
        wp_delete_post($id, true);

    }



    /**
     * static constructor. Start a new BackgroundJob Call Stack
     *
     * @return BackgroundJob
     */
    public static function start()
    {
        $call = new static();

        return $call;
    }



    /**
     * Check to see if this request is processing a background Job
     *
     * @return bool
     */
    public static function doingBackgroundJob()
    {
        return static::$processingId ? true : false;
    }



    /**
     * Add a job to the call Stack
     *
     * @return $this
     */
    function add()
    {
        $this->calls[] = func_get_args();

        return $this;
    }



    /**
     * Save the job and send it for processing.
     */
    function send()
    {

        $id = wp_insert_post([
            'post_type'    => 'genie_background_job',
            'post_content' => base64_encode(serialize($this->calls)),
        ]);

        $url = home_url() . '/?' . static::$getVariable . '=' . $id;
        get_headers($url);
    }

}