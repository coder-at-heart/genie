<?php

namespace Lnk7\Genie;

use Lnk7\Genie\WordPressObjects\Page;
use Lnk7\Genie\WordPressObjects\User;

/**
 * Class Template
 * @package Genie
 */

class Template {

	/**
	 * Twig template to use
	 *
	 * @var
	 */
	protected $twigTemplate;

	/**
	 * variables we need to pass into twig
	 *
	 * @var array
	 */
	protected $vars = [];

	/**
	 * is this a protected template?
	 *
	 * @var bool
	 */
	protected $protected = true;



	/**
	 * static constructor
	 *
	 * @param $template
	 *
	 * @return Template
	 */
	public static function Using( $template ) {

		$template = new static( $template );

		$template->addVar( 'page', Page::getCurrent() );

		return $template;
	}



	/**
	 * Template constructor.
	 *
	 * @param $file
	 */
	function __construct( $file ) {

		$this->twigTemplate = $file;
	}



	/**
	 * Add a variable to be sent to twig
	 *
	 * @param $var
	 * @param $value
	 *
	 * @return $this
	 */
	function addVar( $var, $value ) {

		$this->vars[ $var ] = $value;

		return $this;
	}



	/**
	 * Add variables to be sent to twig
	 *
	 * @param $fields
	 *
	 * @return $this
	 */
	function addVars( $fields ) {

		$this->vars = array_merge( $this->vars, $fields );

		return $this;
	}



	/**
	 * Display the template
	 */
	public function display() {

		global $wp;

		if ( $this->protected ) {


			// here we have the sexy SSO stuff.
			if ( ! is_user_logged_in() ) {

				$current_url = home_url( add_query_arg( [], $wp->request ) );

				$attempts = (int) Session::get( 'authenticationRequests' );
				if ( $attempts < 3 ) {
					Session::set( 'authenticationRequests', $attempts + 1 );

					// Try logging the user in
					$url = home_url( '/?option=mo_adfs_sso_saml_user_login&redirect_to=' . $current_url );
					wp_redirect( $url );
					exit;
				} else {
					//TODO : Create a template
					print "Not Authorised";
					exit;
				}
			}
		}

		//  Make the user available to the template
		if ( is_user_logged_in() ) {
			$this->addVar( '_user', User::getCurrent() );
		}

		echo $this->render();

	}



	/**
	 * Set this template as protected
	 *
	 * @return $this
	 */
	function protect() {

		$this->protected = true;

		return $this;
	}



	/**
	 * render the template
	 *
	 * @return string
	 */
	function render() {

		return View::make( $this->twigTemplate, $this->vars );
	}



	/**
	 * remove protection
	 *
	 * @return $this
	 */
	function unprotect() {

		$this->protected = false;

		return $this;
	}

}