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
 
class Mageho_Atos_Block_Standard_Redirect extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
		$standard = Mage::getModel('atos/method_standard');
		$standard->callRequest();
		
		if ($standard->getError()) 
		{
		    return '<pre>'.$standard->getHtml().'</pre>';
		} else {
			if ($paymentMeans = Mage::getSingleton('atos/session')->getAtosStandardPaymentMeans()) {
				$this->setSelectedMethod($paymentMeans)
					->setFormUrl($standard->getUrl())
					->setHtml($standard->getHtml())
					->setTemplate('mageho/atos/standard/redirect.phtml');
					
				return parent::_toHtml();
			}
		}
    }
}