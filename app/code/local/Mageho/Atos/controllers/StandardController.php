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

class Mageho_Atos_StandardController extends Mageho_Atos_Controller_Action
{
    public function redirectAction()
    {
    	$this->getAtosSession()->setQuoteId($this->getCheckoutSession()->getLastQuoteId());
        $this->loadLayout();
        		
		Mage::dispatchEvent('atos_controller_standard_redirect_render_before', array(
				'checkout_session' => $this->getCheckoutSession(), 
				'request' => $this->getRequest(),
				'layout' => $this->getLayout()
			)
		);
        
		$this->renderLayout();
		
        $this->getCheckoutSession()->unsQuoteId();
        $this->getCheckoutSession()->unsRedirectUrl();
    }
	
	public function cancelAction()
	{
		$this->setAtosResponse($_REQUEST);
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($this->getAtosResponse('order_id'));
		if ($order->getId()) {
			Mage::helper('atos')->reorder($order);
		}
		
		switch ($this->getAtosResponse('response_code')) {
			case '17':
				if ($order->getId()) 
				{
					if (!$status = $this->getAtosPaymentStandard()->getConfig()->order_status_payment_canceled) {
						$status = $order->getStatus();
					}
							
					$order->addStatusToHistory($status, Mage::helper('atos')->__('Order was canceled by customer.'), false);
						
					if ($this->getAtosPaymentStandard()->getConfig()->order_status_payment_canceled == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
						$order->hold();
					}
							
					if ($this->getAtosPaymentStandard()->getConfig()->order_status_payment_canceled == Mage_Sales_Model_Order::STATE_CANCELED && $order->canCancel()) {
						$order->cancel();
					}
							
					$this->saveTransaction($order->getPayment());
					$order->save();
				}
					
				$error = $this->getApiResponse()->getResponseLabel($this->getAtosResponse('response_code')) . '<br />';
				$error.= Mage::helper('atos')->__('Choose an another payment method or contact us by phone at %s to validate your order.', Mage::getStoreConfig('general/store_information/phone'));
					
				$this->getAtosSession()->setRedirectTitle(Mage::helper('atos')->__('Payment has been canceled with success.'));
				$this->getAtosSession()->setRedirectMessage($error);	
				break;
			
			default:
				switch ($this->getAtosResponse('cvv_response_code')) {
					case '4E':
					case '': // specific cvv_response_code for AMEX and FINAREF credit card
						$error = $this->getApiResponse()->getResponseLabel($this->getAtosResponse('response_code')).'<br />';
						$error.= $this->getApiResponse()->getCvvResponseLabel($this->getAtosResponse('cvv_response_code')).'<br />';

						$this->getAtosSession()->setRedirectTitle(Mage::helper('atos')->__('Your order has been refused'));
						$this->getAtosSession()->setRedirectMessage($error);
					break;
					default:
						 $error = $this->getApiResponse()->getResponseLabel($this->getAtosResponse('response_code')).'<br />';
						 $error.= $this->getApiResponse()->getBankResponseLabel($this->getAtosResponse('bank_response_code')).'<br />';
								 
						 $this->getAtosSession()->setRedirectTitle(Mage::helper('atos')->__('Your order has been refused'));
						 $this->getAtosSession()->setRedirectMessage($error);
					break;
				}
				break;
		}
		
		Mage::dispatchEvent('atos_controller_standard_cancel', array(
				'atos_response' => $this->getAtosResponse(),
				'atos_session' => $this->getAtosSession(),
				'checkout_session' => $this->getCheckoutSession(),
				'order' => $order->getId() ? $order : NULL,
				'request' => $this->getRequest()
			)
		);
		
		$this->_redirect('*/*/failure', array('_secure' => true));
	}

	public function normalAction() 
	{
        $this->setAtosResponse($_REQUEST);
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($this->getAtosResponse('order_id'));
		
		switch ($this->getAtosResponse('response_code'))
		{
		    case '00':
                if ($order->getId()) {
                    $order->addStatusToHistory($order->getStatus(), Mage::helper('atos')->__('Customer returned successfully from payment platform.'))
                          ->save();
                }
		    
				$this->getCheckoutSession()->getQuote()->setIsActive(false)->save();
                $this->_redirect('checkout/onepage/success', array('_secure' => true));
                return;
			    break;
			default:
				switch ($this->getAtosResponse('cvv_response_code')) {
					case '4E':
					case '': // specific cvv_response_code for AMEX and FINAREF credit card
						$error = $this->getApiResponse()->getResponseLabel($this->getAtosResponse('response_code')).'<br />';
						$error.= $this->getApiResponse()->getCvvResponseLabel($this->getAtosResponse('cvv_response_code')).'<br />';
						
						$this->getAtosSession()->setRedirectMessage($error);
					break;
					default:
						$error = $this->getApiResponse()->getResponseLabel($this->getAtosResponse('response_code')).'<br />';
						$error.= $this->getApiResponse()->getBankResponseLabel($this->getAtosResponse('bank_response_code')).'<br />';
						 
						$this->getAtosSession()->setRedirectMessage($error);
					break;
				}
				break;
		}
				
		Mage::dispatchEvent('atos_controller_standard_normal', array(
				'atos_response' => $this->getAtosResponse(),
				'atos_session' => $this->getAtosSession(),
				'checkout_session' => $this->getCheckoutSession(),
				'order' => $order->getId() ? $order : NULL,
				'request' => $this->getRequest()
			)
		);
		
		$this->_redirect('*/*/failure', array('_secure' => true));
	}
	
	/**
	* When has error in treatment
	*/
    public function failureAction()
    {
    	$cart = $this->getCart();
    	if (! $cart->getQuote()->getItemsCount()) {
    		$this->_redirect('/');
    		return;
        }
    
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        
        if ($blockAtosPaymentFailure = $this->getLayout()->getBlock('atos.payment.failure')) {
	        $blockAtosPaymentFailure->setTitle($this->getAtosSession()->getRedirectTitle())
	        	->setMessage($this->getAtosSession()->getRedirectMessage());
        }
        
        $paymentMeans = $this->getAtosSession()->getAtosStandardPaymentMeans();
        
   		Mage::dispatchEvent('atos_controller_standard_failure_render_before', array(
				'atos_session' => $this->getAtosSession(),
				'checkout_session' => $this->getCheckoutSession(),
				'block' => $blockAtosPaymentFailure,
				'layout' => $this->getLayout()
			)
		);
        
        $this->getAtosSession()->unsetAll();
        $this->getAtosSession()->setAtosStandardPaymentMeans($paymentMeans);
        
        $this->renderLayout();
    }
}