<?php

namespace Lnk7\Genie\Fields;

class TextAreaField extends TextField
{


    /**
     * The number of rows for this input
     *
     * @param $rows
     *
     * @return $this
     */
    public function rows(int $rows)
    {
        return $this->set('rows', $rows);
    }


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('textarea');
        $this->newLines('');
    }


    /**
     * Decides how to render new lines. Detauls to 'wpautop'. Choices of 'wpautop' (Automatically add paragraphs), 'br' (Automatically add <br>) or '' (No Formatting)
     *
     * @param $newLines  string wpautop|br|ni;;
     *
     * @return $this
     */
    public function newLines(string $newLines)
    {
        return $this->set('new_lines', $newLines);
    }

}
