<?php

namespace Lnk7\Genie\Traits;

trait HasData
{


    /**
     * Used to store the post data
     *
     * @var array
     */
    protected $data;


    /**
     * magic getter
     *
     * @param $var
     *
     * @return mixed
     */
    public function __get($var)
    {
        if (array_key_exists($var, $this->data)) {
            return $this->data[$var];
        }

        return false;
    }


    /**
     * magic set
     *
     * @param $var
     * @param $value
     */
    public function __set($var, $value)
    {
        $this->data[$var] = $value;
    }


    /**
     * Return all data for this post
     *
     * @return mixed|void
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Fill data properties from an array
     *
     * @param array $array
     */
    public function fill(array $array)
    {
        foreach ($array as $field => $value) {
            $this->data[$field] = $value;
        }
    }


    /**
     * Needed from twig templates
     *
     * @param $var
     *
     * @return bool
     */
    public function __isset($var)
    {
        return array_key_exists($var, $this->data);
    }


}