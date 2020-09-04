<?php

namespace Lnk7\Genie\Utilities;


class AddShortcode

{

    /**
     * the name of the shortcode
     *
     * @var string
     */
    protected $shortcode;



    /**
     * constructor.
     *
     * @param string $shortcode
     */
    public function __construct(string $shortcode)
    {
        $this->shortcode = $shortcode;

    }



    /**
     * Static constructor
     *
     * @param $shortcode
     *
     * @return static
     */

    public static function called(string $shortcode)
    {
        return new static($shortcode);

    }



    /**
     * Set the callback and register the actions and filters
     *
     * @param callable $callback
     */
    public function run(callable $callback)
    {
        add_shortcode($this->shortcode, $callback);
    }

}