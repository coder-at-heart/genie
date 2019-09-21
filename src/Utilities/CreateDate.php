<?php

namespace Lnk7\Genie\Utilities;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Class CreateDate
 *
 * Handy function to handle dates. Allows chaining.
 *
 * e.g.
 * CreateDate::From('now')->format('Y-m-d H-i-s');
 *
 * @package Lnk7\Genie\Utilities
 */
class CreateDate {

    private $date;



    /**
     * Static constructor
     *
     * @param null $date
     *
     * @return static
     * @throws Exception
     */
    public static function From( $date = null ) {

        if ( ! $date instanceof DateTime ) {
            try {
                $date = new DateTime( $date );
            } catch ( Exception $e ) {
                $date = new DateTime( 'now' );
            }

        }

        return new static( $date );
    }



    /**
     * Create a date from a format
     *
     * @param $date
     * @param $format
     *
     * @return CreateDate
     */
    public static function FromFormat( $date, $format ) {

        return new static ( DateTime::createFromFormat( $format, $date ) );

    }



    /**
     * Create a date from a unix timestamp
     *
     * @param int $timestamp
     *
     * @return CreateDate
     * @throws \Exception
     */
    public static function FromTimestamp( int $timestamp ) {

        $date = new DateTime( "@$timestamp" );

        return new static( $date );
    }



    /**
     * Constructor
     *
     * CreateDate constructor.
     *
     * @param DateTime $date
     */
    public function __construct( DateTime $date ) {

        $this->date = $date;

    }



    /**
     * Set the timezone
     *
     * @param DateTimeZone $tz
     *
     * @return $this
     */
    public function setTimeZone( DateTimeZone $tz ) {

        $this->date->setTimezone( $tz );

        return $this;

    }



    /**
     * Format a date.
     *
     * @param $format
     *
     * @return string
     */
    function format( $format ) {

        return $this->date->format( $format );

    }

}