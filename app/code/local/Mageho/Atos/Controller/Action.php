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
 
class Mageho_Atos_Controller_Action extends Mage_Core_Controller_Front_Action
{
    protected $_atosPaymentResponse;
    
   /*
	* Get Atos/Sips Standard config
	*
	* @return Quadra_Atos_Model_Config
	*/
	public function getConfig()
	{
	    return Mage::getSingleton('atos/config');
	}
	
	/*
	 * Get checkout session
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	public function getCheckoutSession()
	{
	    return Mage::getSingleton('checkout/session');	
	}

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

   /*
	* Get customer session
	*
	* @return Mage_Customer_Model_Session
	*/
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
	/*
     * Get singleton Atos session
     *
     * @return object Mageho_Atos_Model_Session
     */
	public function getAtosSession()
	{
	    return Mage::getSingleton('atos/session');	
	}
	
   /*
	* Get Atos Api Response Model
	*
	* @return Mageho_Atos_Model_Api_Response
	*/
    public function getApiResponse()
    {
        return Mage::getSingleton('atos/api_response');
    }
    
	/*
     * Get singleton with Atos standard
     *
     * @return object Mageho_Atos_Model_Method_Standard
     */
    public function getAtosPaymentStandard()
    {
        return Mage::getSingleton('atos/method_standard');
    }
    
    /**
     * Get singleton with Atos several
     *
     * @return object Mageho_Atos_Model_Method_Several
     */
    public function getAtosPaymentSeveral()
    {
        return Mage::getSingleton('atos/method_several');
    }
    	
	/**
     * @param array $request
     * @return object Mageho_Atos_Controller_Action $this
     */
    public function setAtosResponse($request)
    {
    	if (! isset($request['DATA'])) {
	    	Mage::getSingleton('atos/debug')->log($this->__('An error occured: var $request has no data.'));
            $this->getAtosSession()->setRedirectMessage($this->__('An error occured: no data received.'));
            $this->_redirect('*/*/failure', array('_secure' => true));
            return;
        }
        
        $response = $this->getApiResponse()->doResponse($request['DATA']);
    
		if ($response['merchant_id'] != $this->getConfig()->merchant_id) {
			Mage::getSingleton('atos/debug')->log($this->__("Configuration merchant id (%s) doesn't match merchant id (%s)", $this->getConfig()->merchant_id, $response['merchant_id']));
			$this->getAtosSession()->setRedirectMessage($this->__('We encounter errors with this payment method'));
	    	$this->_redirect('*/*/failure', array('_secure' => true));
            return;
		}
	
	    if ($response['code'] == '-1') {
	    	Mage::getSingleton('atos/debug')->log($this->__("An error occured: error code %s", $this->getAtosResponse('code')));
			$this->getAtosSession()->setRedirectMessage($this->__('We encounter errors with this payment method'));
	    	$this->_redirect('*/*/failure', array('_secure' => true));
	        return;
	    }
		
		$this->_atosPaymentResponse = $response;

		return $this;
    }
	
	public function hasAtosResponse() 
	{
		return (bool) !empty($this->_atosPaymentResponse) && count($this->_atosPaymentResponse);	
	}

	public function getAtosResponse($key = null) 
	{
		if ($key != null) {
			if (isset($this->_atosPaymentResponse[$key])) {
				return $this->_atosPaymentResponse[$key];
			}
		}
		return $this->_atosPaymentResponse;
	}
	
	/*
	 *
	 * Mise à jour de la commande selon la réponse du serveur bancaire
	 * Option BO : Création de la facture
	 * Envoie de l'email de confirmation de commande
	 * Sauvegarde de la transaction dans le BO Magento
	 *
	 * @param Mage_Sales_Model_Order $order
	 * @return
	 *
	 */
	protected function _updateOrderState(Mage_Sales_Model_Order $order) 
	{
		if (! $order->getId()) {
			return;
		}
		
		/* Retrieve payment method object */
		$payment = $order->getPayment()->getMethodInstance();
		
        switch ($this->getAtosResponse('response_code')) {
            // Success order
            case '00':
                if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
                	$order->unhold();
                }
                
                /*
                 * Dans l'eventualité où aucun statut de commande serait configuré 
                 * depuis le BO ATOS pour un paiement accepté
                 */
                if (!$status = $payment->getConfig()->order_status_payment_accepted) {
                	$status = $order->getStatus();
                }

                $message = Mage::helper('atos')->__('Payment accepted by Atos');

                if ($status == Mage_Sales_Model_Order::STATE_PROCESSING) {
                    $order->setStatus(
                    	Mage_Sales_Model_Order::STATE_PROCESSING, $status, $message
                    );
                } else if ($status == Mage_Sales_Model_Order::STATE_COMPLETE) {
                    $order->setStatus(
                    	Mage_Sales_Model_Order::STATE_COMPLETE, $status, $message, null, false
                    );
                } else {
                	$order->addStatusToHistory($status, $message, true);
                }

                // Create invoice
				if ($payment->getConfig()->invoice_create) {
                    Mage::helper('atos')->saveInvoice($order);
				}

                if (!$order->getEmailSent()) {
                	$order->sendNewOrderEmail();
                }
                break;

            default:
                // Cancel order
                $messageError = Mage::helper('atos')->__('Customer was rejected by Atos');

                if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
                    $order->unhold();
                }

                /*
                 * Dans l'eventualité où aucun statut de commande serait configuré depuis le BO ATOS pour un paiement refusé
                 */
                if (!$state = $payment->getConfig()->order_status_payment_refused) {
                    $state = $order->getState();
                }

                $order->addStatusToHistory($state, $messageError);

                if ($state == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
                	$order->hold();
                }
                break;
        }
        
        $order->save();
    }
	
	public function saveTransaction(Varien_Object $payment)
	{
		$atosPaymentResponse = $this->getAtosResponse();
		$data = serialize($atosPaymentResponse);
		
		$payment->setCcType($atosPaymentResponse['payment_means'])
			->setCcTransId($atosPaymentResponse['transaction_id'])
			->setAdditionalData($data);
			
		if ($ccNumber = $this->getApiResponse()->getCcNumberEnc($atosPaymentResponse['card_number'])) {
			$payment->setCcNumberEnc($ccNumber);
		}
		if ($ccLast4 = $this->getApiResponse()->getCcLast4($atosPaymentResponse['card_number'])) {
			$payment->setCcLast4($ccLast4);
		}
		
		// Transaction acceptée
		if ($atosPaymentResponse['response_code'] == '00') {
			$transactionType = Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER;
		} else {
		// Transaction refusée
			$transactionType = Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID;
		}
		
		$transactionDetails = array(
        	'is_transaction_closed' => 1
        );
		
		$this->_addTransaction($payment, $atosPaymentResponse['transaction_id'], $transactionType, $transactionDetails, $atosPaymentResponse);
		$payment->save();

		return $this;
	}
	
	/**
     * Add payment transaction
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param string $transactionId
     * @param string $transactionType
     * @param array $transactionDetails
     * @param array $transactionAdditionalInfo
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    protected function _addTransaction(Mage_Sales_Model_Order_Payment $payment, $transactionId, $transactionType,
        array $transactionDetails = array(), array $transactionAdditionalInfo = array(), $message = false
    ) {

        $payment->setTransactionId($transactionId);
        $payment->resetTransactionAdditionalInfo();
        foreach ($transactionDetails as $key => $value) {
            $payment->setData($key, $value);
        }
		
        if ($transactionAdditionalInfo) {
            $payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $transactionAdditionalInfo);
        }
		
        $transaction = $payment->addTransaction($transactionType, null, false , $message);
        foreach ($transactionDetails as $key => $value) {
            $payment->unsetData($key);
        }
        $payment->unsLastTransId();

        /**
         * It for self using
         */
        $transaction->setMessage($message);

        return $transaction;
    }
 }
