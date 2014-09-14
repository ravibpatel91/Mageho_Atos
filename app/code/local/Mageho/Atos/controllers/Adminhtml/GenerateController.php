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

class Mageho_Atos_Adminhtml_GenerateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	$isAjax = Mage::app()->getRequest()->isAjax();
        if (! $isAjax) {
        	return;
        }
        
    	 $this->getResponse()->clearHeaders()
    	 	->setHeader('Content-type', 'application/json', true);
    	
    	$merchant_ids = Mage::getModel('atos/system_config_source_merchantid')->getMerchantIds();
    	
    	if (! count($merchant_ids)) {
        	$this->getResponse()->setBody(
        		Mage::helper('core')->jsonEncode(
        			array('message' => 
        				Mage::helper('atos')->__('First download your certificates to generate the files.')
        			)
        		)
        	);
    	}
    	
        $directoryPath = Mage::getModel('atos/api_files')->getLibDir();
        
    	$messages = '';
    	foreach ($merchant_ids as $merchant_id) {
    		$pathfileFilename = 'pathfile.' . $merchant_id;
    		$parcomFilename = 'parcom.' . $merchant_id;
    		
    		if (! file_exists($directoryPath . $pathfileFilename) && ! file_exists($directoryPath . $parcomFilename)) {
    			Mage::getModel('atos/api_files')->generatePathfile($pathfileFilename);
				$messages.= Mage::helper('atos')->__('Pathfile %s & Parcom %s was generated with success.', $pathfileFilename, $parcomFilename) . '<br />';
			} else {
				$messages.= Mage::helper('atos')->__('Pathfile %s & Parcom %s already exists. Delete them if you want to generate it again.', $pathfileFilename, $parcomFilename) . '<br />';
			}
        }
        
        $this->getResponse()->setBody(
        	Mage::helper('core')->jsonEncode(
        		array('message' => $messages)
        	)
        );
    }
}