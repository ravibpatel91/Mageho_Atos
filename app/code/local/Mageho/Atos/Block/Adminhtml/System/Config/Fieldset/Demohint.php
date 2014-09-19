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

class Mageho_Atos_Block_Adminhtml_System_Config_Fieldset_Demohint 
	extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	/*
    	 *
    	 * On n'affiche pas les astuces du mode démonstration si le certificat configuré est un certificat de test
    	 *
    	 */
    	if (!Mage::getSingleton('atos/config')->isTestMode()) {
	    	return '';
    	}
    
		$hlpr = Mage::helper('atos');
		
		$html = $this->_getHeaderHtml($element);

		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		
		$fields = array(
			array(
				'label' => $hlpr->__('Card number'),
				'value' => '4974934125497800',
				'comment' => $hlpr->__('Bank Response Code') . ' : 00 (' . $hlpr->__('Authorization accepted') . ')'
			),
			array(
				'label' => $hlpr->__('Card number'),
				'value' => '4972187615205',
				'comment' => $hlpr->__('Bank Response Code') . ' : 05 (' . $hlpr->__('Permission denied') . ')'
			),
			array(
				'label' => $hlpr->__('CVV'),
				'value' => '600',
				'comment' => 'ccv_response_code : 4D'
			),
			array(
				'label' => $hlpr->__('CVV'),
				'value' => '640',
				'comment' => 'ccv_response_code : 4D'
			),
			array(
				'label' => $hlpr->__('CVV'),
				'value' => '650',
				'comment' => 'ccv_response_code : 50'
			),
			array(
				'label' => $hlpr->__('CVV'),
				'value' => '653',
				'comment' => 'ccv_response_code : 53'
			),
			array(
				'label' => $hlpr->__('CVV'),
				'value' => '645',
				'comment' => 'ccv_response_code : 55'
			)
		);

		$html .= '<p>'.$hlpr->__('Test credit card & CVV').'</p>';
		
        foreach ($fields as $field) {
        	$html.= $this->_getFieldHtml($element, $field);
        }
		
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }

    protected function _getFieldHtml($fieldset, $field)
    {
    	$code = 'hint-' . uniqid();
    
        $field = $fieldset->addField(
        	$code, 
        	'label',
            array(
                'name' => $code,
                'label' => $field['label'],
                'value' => $field['value'],
				'comment' => $field['comment']
            )
        )->setRenderer($this->_getFieldRenderer());

		return $field->toHtml();
    }
}
