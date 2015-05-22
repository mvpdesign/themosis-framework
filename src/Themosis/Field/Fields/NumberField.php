<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

class NumberField extends FieldBuilder{

    /**
     * Define a core TextField.
     *
     * @param array $properties The text field properties.
     */
    public function __construct(array $properties)
    {
        parent::__construct($properties);

        $this->fieldType();
    }

    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = 'number';
    }

    /**
     * Handle the field HTML code for metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        return View::make('metabox._themosisNumberField', array('field' => $this))->render();
    }

    /**
     * Handle the field HTML code for the Settings API output.
     *
     * @return string
     */
    public function page()
    {
        return $this->metabox();
    }

}