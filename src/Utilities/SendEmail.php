<?php

namespace Lnk7\Genie\Utilities;

use InlineStyle\InlineStyle;

/**
 * Class SendEmail
 *
 * A Handy wrapper around wp_mail (https://developer.wordpress.org/reference/functions/wp_mail/)
 *
 * $body = View::make('Emails/example.twig',['name' => 'Sunil]);
 *
 * SendEmail::to('someone@somewhere.com')
 *   ->from('from@someone.com')
 *   -<body($body)
 *   ->subject('test email')
 *   ->send();
 *
 * @package Lnk7\Genie
 */
class SendEmail {

    var $to;

    var $subject;

    var $headers = [
        'Content-Type: text/html; charset=UTF-8',
    ];

    var $body = '';

    var $attachments = [];



    /**
     * Static constructor
     *
     * @return static
     */
    public static function to( $to ) {

        $email = new static();
        $email->to( $to );

        return $email;
    }



    /**
     * Sets the sender of the email
     *
     * @param        $email
     * @param string $name
     *
     * @return $this
     */
    function from( $email, $name = '' ) {

        $this->addHeader( "From: $name <{$email}>" );

        return $this;
    }



    /**
     * Adds a header to the email
     *
     * @param $header
     */
    function addHeader( $header ) {

        $this->headers[] = $header;
    }



    /**
     * Add an attachment to the email message
     *
     * @param $file
     *
     * @return $this
     */
    public function addAttachment( $file ) {

        $this->attachments[] = $file;

        return $this;
    }



    /**
     * Sets the body of the email
     *
     * @param $body
     *
     * @return $this
     */
    function body( $body ) {

        $this->body = $body;

        return $this;
    }



    /**
     * Send the email using wp_mail
     *
     * @return bool
     */
    function send() {

        return wp_mail( $this->to, $this->subject, $this->format(), $this->headers, $this->attachments );

    }



    /**
     * Inline Styles from the email
     *
     * @return string
     */
    function format() {

        $htmlDoc = new InlineStyle( $this->body );
        $htmlDoc->applyStylesheet( $htmlDoc->extractStylesheets() );

        return $htmlDoc->getHTML();

    }


    /**
     * Sets the subject of the email
     *
     * @param $subject
     *
     * @return $this
     */
    function subject( $subject ) {

        $this->subject = $subject;

        return $this;
    }

}