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

class Mageho_Atos_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    public function saveInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $convertor = Mage::getModel('sales/convert_order');
			$invoice = $convertor->toInvoice($order);
                       
			foreach ($order->getAllItems() as $orderItem) 
			{
                try {   
                    $item = $convertor->itemToInvoiceItem($orderItem);
					/// Prise en charge des produits packages sans quantité réelle
                    $item->setQty($orderItem->isDummy() ? 1 : $orderItem->getQtyToInvoice()); 
                    $invoice->addItem($item);
                } catch (Exception $e) {}
            }
			   
            $invoice->collectTotals();
            $invoice->register();
                      
		    Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->addObject($invoice->getOrder())
              ->save();
						 
            $order->addStatusToHistory($order->getStatus(), Mage::helper('atos')->__('Invoice %s was created', $invoice->getIncrementId()), true);
			return true;
        } else {
        	return false;
        }
    }

    /**
     *  Fill checkout cart with current order id
     *
     *  @param string|object $order
     */
	public function reorder($order)
    {
        if (is_numeric($order)) {
        	$order = Mage::getModel('sales/order')->load($order);
        }
        
        $cart = Mage::getSingleton('checkout/cart');
        $checkoutSession = Mage::getSingleton('checkout/session');

        if ($order->getId()) {
            $items = $order->getItemsCollection();
            foreach ($items as $item) {
                try {
                    $cart->addOrderItem($item);
                } catch (Mage_Core_Exception $e) {
                    if ($checkoutSession->getUseNotice(true)) {
                        $checkoutSession->addNotice($e->getMessage());
                    } else {
                        $checkoutSession->addError($e->getMessage());
                    }
                } catch (Exception $e) {
                    $checkoutSession->addException($e, Mage::helper('checkout')->__('Cannot add the item to shopping cart.'));
                }
            }
        }

        $cart->save();      
    }

	/**
     * Return backend config for element like JSON
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function getElementBackendConfig(Varien_Data_Form_Element_Abstract $element) 
    {
        /* $config = $element->getFieldConfig()->backend_congif; ? mispelled ? */
        $config = $element->getFieldConfig()->backend_config;
        if (!$config) {
            return false;
        }
        $config = $config->asCanonicalArray();
        if (isset($config['enable_for_countries'])) {
            $config['enable_for_countries'] = explode(',', str_replace(' ', '', $config['enable_for_countries']));
        }
        if (isset($config['disable_for_countries'])) {
            $config['disable_for_countries'] = explode(',', str_replace(' ', '', $config['disable_for_countries']));
        }
        return Mage::helper('core')->jsonEncode($config);
    }
	
	public function getNbPayment()
	{
		return (int) Mage::getSingleton('atos/method_several')->getConfig()->nb_payment;	
	}
}