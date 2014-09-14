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
 * @version      Release: 1.0.8.2
 * @license      http://www.opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
 
class Mageho_Atos_Model_Observer
{
    public function adminSystemConfigSavedAtosSection(Varien_Event_Observer $observer)
    {
    	$model = Mage::getModel('salesrule/rule');
    	
    	$data = Mage::app()->getRequest()->getPost();
        $store = $observer->getStore();
    	$website = $observer->getWebsite();
    	
		if ($store) {
            $scope   = 'stores';
            $scopeId = (int)Mage::getConfig()->getNode('stores/' . $store . '/system/store/id');
        } elseif ($website) {
            $scope   = 'websites';
            $scopeId = (int)Mage::getConfig()->getNode('websites/' . $website . '/system/website/id');
        } else {
            $scope   = 'default';
            $scopeId = 0;
        }    
        	
    	$validateResult = $model->validateData(new Varien_Object($data));
        if ($validateResult !== true) {
        	foreach($validateResult as $errorMessage) {
        		Mage::getSingleton('core/session')->addError($errorMessage);
        	}
            # $this->_redirect('*/*/edit', array('_current' => array('section', 'website', 'store')));
            # return;
        } else {
	        if (isset($data['rule'])) 
	        {
	        	if (count($data['rule']['conditions']) > 1) {
					Mage::getConfig()->saveConfig('atos/securecode/conditions', serialize(array('conditions' => $data['rule']['conditions'])), $scope, $scopeId);
				} else {
					Mage::getConfig()->deleteConfig('atos/securecode/conditions', $scope, $scopeId);
				}
				unset($data['rule']);
			}
        }
    }
}