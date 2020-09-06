<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class TaxonomyField extends Field
{


    /**
     * Specify the taxonomy to select terms from. Defaults to 'category'
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
     * Allow no value to be selected
     *
     * @param $allowNull
     *
     * @return $this
     */
    public function allowNull(bool $allowNull)
    {
        return $this->set('allow_null', $allowNull);
    }


    /**
     * Specify the type of value returned by get_field(). Defaults to 'id'.
     * Choices of 'object' (Term object) or 'id' (Term ID)
     *
     * @param string $returnFormat object|id
     *
     * @return $this
     */

    public function returnFormat(string $returnFormat)
    {
        return $this->set('return_format', $returnFormat);
    }


    /**s
     * Load terms from the post?
     *
     * @param bool $loadTerms
     *
     * @return $this
     */
    public function loadTerms(bool $loadTerms)
    {
        return $this->set('load_terms', $loadTerms);
    }


    /**
     * Save terms to the post?
     *
     * @param bool $saveTerms
     *
     * @return $this
     */
    public function saveTerms(bool $saveTerms)
    {
        return $this->set('save_terms', $saveTerms);
    }


    /**
     * Specify if terms added should be added to Wordpress
     *
     * @param bool $addTerms
     *
     * @return $this
     */
    public function addTerms(bool $addTerms)
    {
        return $this->set('add_term', $addTerms);
    }


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('taxonomy');
        $this->fieldType('select');
    }


    /**
     * Specify the appearance of the taxonomy field. Defaults to 'checkbox'.
     * Choices of 'checkbox' (Checkbox inputs), 'multi_select' (Select field - multiple),
     * 'radio' (Radio inputs) or 'select' (Select field)
     *
     * @param $type
     *
     * @return $this
     */
    public function fieldType(string $type)
    {
        return $this->set('field_type', $type);
    }

}