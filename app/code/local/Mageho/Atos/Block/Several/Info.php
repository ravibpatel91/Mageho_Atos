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
 
class Mageho_Atos_Block_Several_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
	{
        parent::_construct();
        $this->setTemplate('mageho/atos/several/info.phtml');
    }

	public function getApiParameters()
	{
	    return $this->getMethod()->getApiParameters();
	}
	
	public function getApiResponse()
	{
	    return $this->getMethod()->getApiResponse();	
	}
	
	public function getSelectedMethod()
	{		
	    return $this->getMethod()->getAtosSession()->getAtosSeveralPaymentMeans();
	}
	
	public function getCreditCardImgSrc($cc = '') 
	{
		if (! isset($cc)) {
			$cc = strtolower($this->getSelectedMethod());
		}
		return Mage::getSingleton('atos/config')->getCardIcon($cc);
	}
}