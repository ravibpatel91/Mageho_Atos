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

class Mageho_Atos_Block_Several_Form extends Mage_Payment_Block_Form
{
	protected $_paymentMeans = array();
	
    protected function _construct()
    {
        parent::_construct();
        
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote) {
	        $totals = $quote->getTotals();
	        $total = $totals['grand_total']->getValue();
	        
	        $nb = Mage::helper('atos')->getNbPayment();	
	        $term = $total / $nb;
	
	        $additional = Mage::getConfig()->getBlockClassName('core/template');
	        $additional = new $additional;
	        
	        $additional->setTerm($term)
	        	->setNb($nb)
	        	->setTotal($total)
	        	->setTemplate('mageho/atos/several/form.additional.phtml');
	        
	        $this->setMethodLabelAfterHtml($additional->toHtml());
        }
        
        $this->setTemplate('mageho/atos/several/form.phtml');
            
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
	
	public function getSelectedMethod()
	{		
	    return $this->getMethod()->getAtosSession()->getAtosSeveralPaymentMeans();
	}
	
	public function getCmsBlock()
	{
		$blockId = $this->getMethod()->getConfig()->cms_block;
		
        $block = Mage::getConfig()->getBlockClassName('cms/block');
        $block = new $block;
        $block->setBlockId($blockId);
		
		return $block->toHtml();
	}
}