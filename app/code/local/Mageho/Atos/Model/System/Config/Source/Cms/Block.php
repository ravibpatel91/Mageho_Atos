<?php
class Mageho_Atos_Model_System_Config_Source_Cms_Block
{
    protected $_options;

    public function toOptionArray()
    {
        if (! $this ->_options)
        {
        	$options = Mage::getModel('cms/block')->getCollection()
            	->load()
            	->toOptionArray();
        
            $this->_options = array(array('value' => '', 'label' => Mage::helper('catalog')->__('Please select static block ...')));
            $this->_options = array_merge($this->_options, $options);
        }
        return $this->_options;
    }
} 