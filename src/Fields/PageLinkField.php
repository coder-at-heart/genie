<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class PageLinkField extends Field
{


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


    /**
     * Allow Archives
     *
     * @param bool $allowArchives
     *
     * @return $this
     */
    public function allowArchives(bool $allowArchives)
    {
        return $this->set('allow_archives', $allowArchives);
    }


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('page_link');
        $this->metaQuery('NUMERIC');
        $this->postObject(['page']);
    }


    /**
     * Specify an array of post types to filter the available choices. Defaults to ''
     *
     * @param array $postObject
     *
     * @return $this
     */
    public function postObject(array $postObject)
    {
        return $this->set('post_type', $postObject);
    }

}