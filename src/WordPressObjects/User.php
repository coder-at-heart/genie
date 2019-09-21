<?php

namespace Lnk7\Genie\WordPressObjects;

use JsonSerializable;
use WP_User;

/**
 * Class User
 * @package Vitol\WordPressObjects
 */
class User implements JsonSerializable {

    /**
     * @var WP_User
     */
    private $user;



    /**
     * TODO: not happy about this
     */
    public static function Setup() {
        do_action( 'genie_user_setup' );
    }



    /**
     * find a user by email address
     *
     * @param $email
     *
     * @return User
     */
    public static function findByEmail( $email ) {

        return new static( $email );
    }



    /**
     * Returns the current logged in user
     *
     * @return User
     */
    public static function getCurrent() {

        return new static();
    }



    /**
     * User constructor.
     *
     * @param null|WP_User|string $user
     */
    function __construct( $user = null ) {

        // How do we work out who is using the site?
        if ( is_null( $user ) ) {
            $user = get_user_by( 'id', get_current_user_id() );
        } else if ( is_int( $user ) ) {
            $user = get_user_by( 'id', $user );
        } else if ( filter_var( $user, FILTER_VALIDATE_EMAIL ) ) {
            $user = get_user_by( 'email', $user );
        }

        if ( $user ) {
            $acfKey = 'user_' . $user->ID;
            $fields = get_fields( $acfKey );
            foreach ( $fields as $key => $value ) {
                $user->$key = $value;
            }
            $this->user = $user;
        }

    }



    /**
     * Passthrough to the user magic function
     *
     * @param $key
     *
     * @return mixed
     */

    public function __get( $key ) {

        return $this->user->$key;
    }



    /**
     * Passthrough to the user magic function
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function __set( $key, $value ) {

        return $this->user->$key = $value;
    }



    /**
     * magic isset function call
     */
    public function __isset( $key ) {

        return isset( $this->user->$key );
    }



    /**
     * Return the WordPress user object
     *
     * @return bool|string|WP_User|null
     */

    function getUser() {

        return $this->user;
    }



    /**
     * What should we convert to json ?
     */
    public function jsonSerialize() {

        return $this->user->data;
    }

}