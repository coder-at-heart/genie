<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class RelationshipField extends Field
{

    protected $type = 'relationship';



    /**
     * Specify an array of post types to filter the available choices. Defaults to ''
     *
     * @param array $postObject
     *
     * @return $this
     */
    public function postObject(array $postObject)
    {
        $this->set('post_type', $postObject);
        $this->returnFormat('id');

        return $this;
    }



    /**
     * Specify the type of value returned by get_field(). Defaults to 'object'.
     * Choices of 'object' (Post object) or 'id' (Post ID)
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
     * Specify the minimum posts required to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function min(int $number)
    {
        return $this->set('min', $number);

    }



    /**
     * Specify the maximum posts allowed to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function max(int $number)
    {
        return $this->set('max', $number);

    }



    protected function setDefaults()
    {
        parent::setDefaults();
        $this->filters(['search']);
        $this->elements(['featured_image']);
    }



    /**
     *  Specify the available filters used to search for posts. Choices of 'search' (Search input), 'post_type' (Post type select) and 'taxonomy' (Taxonomy select)
     *
     * @param array $filters
     *
     * @return $this
     */
    public function filters(array $filters)
    {
        return $this->set('filters', $filters);

    }



    /**
     *  pecify the visual elements for each post. Choices of 'featured_image' (Featured image icon)
     *
     * @param array $elements
     *
     * @return $this
     */
    public function elements(array $elements)
    {
        return $this->set('elements', $elements);

    }

}