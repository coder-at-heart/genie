<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class PostObjectField extends Field
{

    protected $type = 'post_object';

    protected $metaQuery = 'NUMERIC';



    /**
     * Specify an array of post types to filter the available choices. Defaults to ''
     *
     * @param array|string $postObject
     *
     * @return $this
     */
    public function postObject($postObject)
    {
        if (!is_array($postObject)) {
            $postObject = [$postObject];
        }

        return $this->set('post_type', $postObject);

    }



    /**
     * Specify an array of taxonomies to filter the available choices. Defaults to ''
     *
     * @param string $taxonomy
     *
     * @return $this
     */
    public function taxonomy(string $taxonomy)
    {
        return $this->set('taxonomy', $taxonomy);

    }



    protected function setDefaults()
    {
        parent::setDefaults();
        $this->returnFormat('id');
        $this->allowNull(false);
        $this->multiple(false);
    }



    /**
     * Specify the type of value returned by get_field(). Defaults to 'object'. Choices of 'object' (Post object) or 'id' (Post ID)
     *
     * @param string $returnFormat object|id
     *
     * @return $this
     */
    public function returnFormat(string $returnFormat)
    {
        return $this->set('return_format', $returnFormat);

    }



    /**
     * Specify if null can be accepted as a value.
     *
     * @param bool $allowNull
     *
     * @return $this
     */
    public function allowNull(bool $allowNull)
    {
        return $this->set('allow_null', $allowNull);

    }



    /**
     * Allow multiple values to be selected
     *
     * @param bool $multiple
     *
     * @return $this
     */
    public function multiple(bool $multiple)
    {
        return $this->set('multiple', $multiple);

    }

}