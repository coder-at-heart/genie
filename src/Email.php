<?php

namespace Lnk7\Genie;

use InlineStyle\InlineStyle;

/**
 * Class Email
 *
 * @author  Sunil Kumar
 * @package Cote\Library
 */
class Email {

	var $to;

	var $subject;

	var $headers = [
		'Content-Type: text/html; charset=UTF-8',
	];

	var $body = '';

	var $attachments = [];


	public static function Setup() {

		Procedures::register( 'email', function () {

			$data  = file_get_contents( 'php://input' );
			$email = unserialize( ( base64_decode( $data ) ) );
			$email->sendForReal();
			print "Email Sent";
			wp_die();

		} );

		Procedures::register( 'test_email', function () {

			$body = Template::Using( 'admin/emails/form_handler.twig' )
				->addvar( 'formData', [
					(object)['name'=>'Sunil','value'=>'jaiswal'],
					(object)['name'=>'3245','value'=>'324543254325435'],
					(object)['name'=>'3425','value'=>'32454545345 2345432543'],
					(object)['name'=>'3245','value'=>'2435234532454325 34 23454325'],
					(object)['name'=>'2345','value'=>'2345'],
					] )
				->render();

			$format = Email::new()
				->to( 'szk@vitol.com' )
				->subject( 'subject' )
				->body( $body )
				->format();

			print $format;
			wp_die();
		} );

	}


	public function addAttachment($file) {



		$this->attachments[] = $file;
		return $this;
	}



	/**
	 * Provides a nice Syntax
	 *
	 * Email::new()->to('')->subject->()->send();
	 *
	 * @return Email
	 */
	public static function new() {

		$email = new static();
		$email->from( 'myvitol@vitol.com', 'My Vitol' );

		return $email;
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



	function format() {

		$htmlDoc = new InlineStyle( $this->body );
		$htmlDoc->applyStylesheet( $htmlDoc->extractStylesheets() );

		return $htmlDoc->getHTML();

	}



	/**
	 * Sets the send er of the mail
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



	function send() {

		$email_server = Settings::get( 'email_server' );

		if ( $email_server ) {

			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_URL, $email_server );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, base64_encode( serialize( $this ) ) );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: text/plain' ] );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );

			$result = curl_exec( $ch );
			$err    = curl_errno( $ch );
			$errmsg = curl_error( $ch );
			curl_close( $ch );

			return compact( 'result', 'err', 'errmsg' );

		}

		return false;

	}



	/**
	 * Sends the email
	 *
	 * @return bool
	 */
	function sendForReal() {

		return wp_mail( $this->to, $this->subject, $this->format(), $this->headers, $this->attachments );
	}



	/**
	 * Pull a list of emails address from the Cote Site Options.
	 *
	 * @param $emailAddresses
	 *
	 * @return $this
	 */
	function setRecipients( $emailAddresses ) {

		$this->to = explode( ',', $emailAddresses );

		return $this;
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



	/**
	 * Adds a single address or multiples (as an array)
	 *
	 * @param $to
	 *
	 * @return $this
	 */

	function to( $to ) {

		$this->to = $to;

		return $this;
	}

}