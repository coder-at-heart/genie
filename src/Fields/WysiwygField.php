<?php

namespace Lnk7\Genie\Fields;

class WysiwygField extends TextField
{


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('wysiwyg');
        $this->tabs('all');
        $this->toolbar('basic');
        $this->mediaUpload(false);
    }


    /**
     * Specify which tabs are available. Defaults to 'all'. Choices of 'all' (Visual & Text), 'visual' (Visual Only) or text (Text Only)
     *
     * @param $tabs
     *
     * @return $this
     */
    public function tabs(string $tabs)
    {
        return $this->set('tabs', $tabs);
    }


    /**
     * Specify the editor's toolbar. Defaults to 'full'.
     * Choices of 'full' (Full), 'basic' (Basic) or a custom toolbar (https://www.advancedcustomfields.com/resources/customize-the-wysiwyg-toolbars/)
     *
     * @param string $toolbar
     *
     * @return $this
     */
    public function toolbar(string $toolbar)
    {
        return $this->set('toolbar', $toolbar);
    }


    /**
     * Hide the medias upload button?
     *
     * @param bool $mediaUpload
     *
     * @return $this
     */
    public function mediaUpload(bool $mediaUpload)
    {
        return $this->set('media_upload', $mediaUpload);
    }

}