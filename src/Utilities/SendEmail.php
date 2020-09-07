<?php

namespace Lnk7\Genie\Utilities;

use InlineStyle\InlineStyle;

/**
 * Class SendEmail
 * A Handy wrapper around wp_mail (https://developer.wordpress.org/reference/functions/wp_mail/)
 * $body = View::make('Emails/example.twig',['name' => 'Sunil]);
 * SendEmail::to('someone@somewhere.com')
 *   ->from('from@someone.com')
 *   ->body($body)
 *   ->subject('test email')
 *   ->send();
 *
 * @package Lnk7\Genie
 */
class SendEmail
{


    /**
     * To email address
     *
     * @var array|string
     */
    protected $email;


    /**
     * The name of the person reciving the email
     *
     * @var string
     */
    protected $name;


    /**
     * The subject
     *
     * @var string
     */
    protected $subject;


    /**
     * Additional headers to send with th eemail
     *
     * @protected string[]
     */
    protected $headers = [
        'Content-Type: text/html; charset=UTF-8',
    ];


    /**
     * The email body (HTML)
     *
     * @var string
     */
    protected $body = '';


    /**
     * A list of attachments
     *
     * @var array
     */
    protected $attachments = [];


    /**
     * Should CSS Styles be inlined?
     *
     * @var bool
     */
    protected $inlineStyles = true;


    /**
     * SendEmail constructor.
     *
     * @param $email
     * @param string $name
     */
    public function __construct($email, $name = '')
    {
        $this->email($email);
        $this->name($name);
    }


    /**
     * Sets the email of the recipient
     *
     * @param $email
     *
     * @return $this
     */
    function email($email)
    {
        $this->email = $email;

        return $this;
    }


    /**
     * Sets the name of the email recipient
     *
     * @param $name
     *
     * @return $this
     */
    function name($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * Static constructor
     * SendEmail::to('someonbe@somedomains.com')
     * ->body('...')
     * ->subject('....')
     * ->send()
     *
     * @param string|array $email
     * @param string $name
     *
     * @return static
     */
    public static function to($email, string $name = '')
    {
        return new static($email, $name = '');
    }


    /**
     * Sets the sender of the email
     *
     * @param string $email
     * @param string $name
     *
     * @return $this
     */
    function from(string $email, string $name = '')
    {
        $this->addHeader("From: $name <{$email}>");

        return $this;
    }


    /**
     * Adds a header to the email
     *
     * @param string $header
     */
    function addHeader(string $header)
    {
        $this->headers[] = $header;
    }


    /**
     * Add an attachment to the email message
     *
     * @param $file
     *
     * @return $this
     */
    public function addAttachment($file)
    {
        $this->attachments[] = $file;

        return $this;
    }


    /**
     * Sets the body of the email
     *
     * @param string $body
     *
     * @return $this
     */
    function body(string $body)
    {
        $this->body = $body;

        return $this;
    }


    /**
     * Send the email using wp_mail
     *
     * @return bool
     */
    function send()
    {
        return wp_mail($this->email, $this->subject, $this->format(), $this->headers, $this->attachments);
    }


    /**
     * Inline Styles from the email
     *
     * @return string
     */
    function format()
    {
        if (!$this->inlineStyles) {
            return $this->body;
        }

        $htmlDoc = new InlineStyle($this->body);
        $htmlDoc->applyStylesheet($htmlDoc->extractStylesheets());

        return $htmlDoc->getHTML();
    }


    /**
     * Run the inline styler?
     *
     * @param $bool
     *
     * @return $this
     */
    function inlineStyles($bool)
    {
        $this->inlineStyles = $bool;

        return $this;
    }


    /**
     * Sets the subject of the email
     *
     * @param $subject
     *
     * @return $this
     */
    function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

}