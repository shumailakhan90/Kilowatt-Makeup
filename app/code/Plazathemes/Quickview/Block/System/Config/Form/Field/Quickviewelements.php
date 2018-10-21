<?php

namespace Plazathemes\Quickview\Block\System\Config\Form\Field;

class Quickviewelements extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('container', ['label' => __('Container'), 'style' => "width: 110px;font-size:11px;"]);
        $this->addColumn('item', ['label' => __('Item Identifier'), 'style' => "width: 110px;font-size:11px;"]);
        $this->addColumn('element', ['label' => __('Button Identifier'), 'style' => "width: 110px;font-size:11px;"]);
        $this->addColumn('link', ['label' => __('Link Identifier'), 'style' => "width: 110px;font-size:11px;"]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add \Exception');
        parent::_construct();
    }
}