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
 
class Mageho_Atos_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    protected $_order;
    protected $_quote;

    /**
     * Get Config model
     *
     * @return object Mageho_Atos_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel('atos/config');
    }
	
	/**
     * Get Debug model
     *
     * @return object Mageho_Atos_Model_Debug
     */
	public function getDebug() 
	{
	    return Mage::getSingleton('atos/debug');
	}
	
	/**
     * Get customers session namespace
     *
     * @return Mage_Customer_Model_Session
     */
	public function getCustomerSession()
	{
	    return Mage::getSingleton('customer/session');	
	}

    /**
     * Get checkout session namespace
     *
     * @return object Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
	
	/**
     * Get atos session namespace
     *
     * @return Mageho_Atos_Model_Session
     */
    public function getAtosSession()
    {
        return Mage::getSingleton('atos/session');
    }
	
	/**
     * Get method Atos Payment Website Standard
     *
     * @return object Mageho_Atos_Model_Method_Standard
     */
    public function getAtosPaymentStandard()
	{
	    return Mage::getSingleton('atos/method_standard');
	}
	
    /**
     * Get Atos API Request Model
     *
     * @return object Mageho_Atos_Model_Api_Request
     */
    public function getApiRequest()
    {
        return Mage::getSingleton('atos/api_request');
    }
	
    /**
     * Get Atos Api Response Model
     *
     * @return object Mageho_Atos_Model_Api_Response
     */
    public function getApiResponse()
    {
        return Mage::getSingleton('atos/api_response');
    }

    /**
     * Get Atos Api Files Model
     *
     * @return object Mageho_Atos_Model_Api_Files
     */
	public function getApiFiles()
	{
        return Mage::getSingleton('atos/api_files');
	}
	
	/**
     * Return current quote object
     * @return Mageho_Sales_Model_Quote $quote
     */
    protected function _getQuote() 
    {
        if (!$this->_quote) {
            $this->_quote = Mage::getModel('sales/quote')->load(
            	$this->getAtosSession()->getQuoteId()
            );
        }
        return $this->_quote;
    }

	/**
     *  Return current order object
     *
     *  @return	  object
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->loadByIncrementId(
            	$this->getCheckoutSession()->getLastRealOrderId()
            );
        }
        return $this->_order;
    }
    
    /**
     * Get order inrement id
     *
     * @return string
     */
    protected function _getOrderId()
    {
        return $this->_getOrder()->getIncrementId();
    }
	
	/**
     * Get order amount
     *
     * @return string
     */
    protected function _getAmount() 
    {
        if ($this->_getOrder()) {
            $total = $this->_getOrder()->getTotalDue();
        } else {
            $total = 0;
		}
        return number_format($total, 2, '', '');
    }
    
    /**
     * Get customer ID
     *
     * @return int
     */
    protected function _getCustomerId() 
    {
        if ($this->_getOrder()) {
            return (int) $this->_getOrder()->getCustomerId();
        } else {
            return 0;
        }
    }
    
	/**
     * Get customer e-mail
     *
     * @return string
     */
    protected function _getCustomerEmail() 
    {
        if ($this->_getOrder()) {
            return $this->_getOrder()->getCustomerEmail();
        } else {
            return 'undefined';
        }
    }
    
    /**
     * Get customer IP address
     *
     * @return string
     */
    protected function _getCustomerIpAddress() 
    {
        return $this->_getQuote()->getRemoteIp();
    }
}