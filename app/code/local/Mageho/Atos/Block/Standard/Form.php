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

class Mageho_Atos_Block_Standard_Form extends Mage_Payment_Block_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Mageho_Atos_Model_Config::METHOD_ATOS_SIPS_PAYMENT_STANDARD;

    /**
     * Config model instance
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config;
    
	protected $_paymentMeans = array();
	
    protected function _construct()
    {
        parent::_construct();
        
        $this->_config = Mage::getModel('atos/config')->setMethod($this->getMethodCode());
        
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('mageho/atos/standard/mark.phtml')
        	->setMethodTitle($this->_config->title)
        	->setIcon($this->_config->getMethodCardIcon());
            
        $this->setTemplate('mageho/atos/standard/form.phtml')
        	->setMethodTitle('')
        	->setMethodLabelAfterHtml($mark->toHtml());
        	
        return parent::_construct();
    }

	public function getPaymentMeans()
	{
		if (empty($this->_paymentMeans)) {
			foreach ($this->getMethod()->getPaymentMeans() as $key => $value) {
				$this->_paymentMeans[$value] = array(
					'id' => $this->getMethodCode() . '_atos_cc_' . str_replace(' ', '_', strtolower($value)),
					'src' => Mage::getSingleton('atos/config')->getCardIcon(strtolower($value)),
					'alt' => ucwords(strtolower($value))
				);
			}
		}
		return $this->_paymentMeans;
	}

    /**
     * Payment method code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
	
	public function getSelectedMethod()
	{		
	    return $this->getMethod()->getAtosSession()->getAtosStandardPaymentMeans();
	}
	
	public function getCmsBlock()
	{
		$cmsBlockId = $this->getMethod()->getConfig()->cms_block;
		return Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($cmsBlockId)->toHtml();
	}
}