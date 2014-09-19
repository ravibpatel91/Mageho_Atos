<?php
/*
 * Mageho
 * Ilan PARMENTIER
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is available
 * through the world-wide-web at this URL: http://www.opensource.org/licenses/OSL-3.0
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to contact@mageho.com so we can send you a copy immediately.
 *
 * @category     Mageho
 * @package     Mageho_Atos
 * @author       Mageho, Ilan PARMENTIER <contact@mageho.com>
 * @copyright   Copyright (c) 2014  Mageho (http://www.mageho.com)
 * @version      Release: 1.0.8.3
 * @license      http://www.opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */

class Mageho_Atos_Block_Adminhtml_System_Config_Fieldset_Creditcardimages 
	extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_backendModel = 'adminhtml/system_config_backend_image';
    protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$html.= '<p>'.Mage::helper('atos')->__('Credit card image used in customer checkout process to select the credit card type.').'</p>';
		
		$creditCards = Mage::getSingleton('atos/config')->getCreditCardTypes();

        foreach ($creditCards as $key => $value) {
			$card = array('value' => $key, 'name' => $value);
        	$html.= $this->_getFieldHtml($element, $card);
        }

        $html .= $this->_getFooterHtml($element);
        return $html;
	}
	
	protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) 
        {
            $this->_dummyElement = new Varien_Object(array(
				'config_path' => sprintf('atos/images/%s', strtolower($card['value'])),
				'backend_model' => $this->_backendModel,
				'upload_dir' => 'atos',
				'base_url' => 'atos',
				'show_in_default' => 1, 
				'show_in_website' => 1,
				'show_in_store' => 1
			));
        }
        
        return $this->_dummyElement;
    }

    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }

    protected function _getFieldHtml($fieldset, $card)
    {
		$configData = $this->getConfigData();
		$code = strtolower($card['value']);
        $path = 'atos/images/' . $code;
        
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = (string) $this->getForm()->getConfigRoot()->descend($path);
            $inherit = true;
        }

        $field = $fieldset->addField(
        	$code, 
        	'image', 
        	array(
                'name' => sprintf('groups[images][fields][%s][value]', $code),
                'label' => $card['name'],
                'value' => $data,
                'inherit' => $inherit,
                'field_config' => $this->_getDummyElement(),
                'scope' => $this->getForm()->getScope(),
                'scope_id' => $this->getForm()->getScopeId(),
                'scope_label' => $this->getForm()->getScopeLabel($e),
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
            )
        )->setRenderer($this->_getFieldRenderer());
        
		return $field->toHtml();
    }
}