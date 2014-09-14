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
 
class Mageho_Atos_Block_Several_Catalog extends Mage_Catalog_Block_Product_View
{
	protected function _prepareLayout() 
	{
		if ($this->isAvailable()) {
			$headBlock = $this->getLayout()->getBlock('head');
			if ($headBlock) {
				$headBlock->addCss('css/mageho/atos/atos.css');
			}
		}
		return parent::_prepareLayout();
	}
	
	protected function _toHtml()
	{
		if ($this->isAvailable()) {
			return parent::_toHtml();	
		}
	}
	
	public function getNbPayment() 
	{
		return Mage::helper('atos')->getNbPayment();	
	}
	
	public function getSeveralPrice() 
	{
		$product = $this->getProduct();
		$price = $product->getFinalPrice() / $this->getNbPayment();
		return Mage::helper('core')->currency($price);
	}
	
	public function getBlockUrl()
	{
		return Mage::getUrl('atos/several/information', array('_secure' => false));
	}
	
	protected function isAvailable() 
	{
		$product = $this->getProduct();
		
		$minOrderTotal = (float) Mage::getStoreConfig('atos/atoswpseveral/min_order_total');
		$maxOrderTotal = (float) Mage::getStoreConfig('atos/atoswpseveral/max_order_total');
		
		if (! Mage::getStoreConfigFlag('payment/atoswpseveral/active')) {
			return false;
		}
		if ( isset($minOrderTotal) && $minOrderTotal > 0 && $product->getFinalPrice() < $minOrderTotal) {
			return false;
		}
		if ( isset($maxOrderTotal) && $maxOrderTotal > 0 && $product->getFinalPrice() > $maxOrderTotal) {
			return false;
		}
		if ( Mage::helper('catalog')->canApplyMsrp($product) ) {
			return false;
		}
		
		return true;
	}
}