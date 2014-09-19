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

class Mageho_Atos_Model_Method_Standard extends Mageho_Atos_Model_Abstract
{
    protected $_code  = Mageho_Atos_Model_Config::METHOD_ATOS_SIPS_PAYMENT_STANDARD;
    protected $_formBlockType = 'atos/standard_form';
	protected $_infoBlockType = 'atos/standard_info';

    /**
     * Config instance
     * @var Mageho_Atos_Model_Config
     */
    protected $_config = null;

    /**
     * Availability options
     */
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
	
	/**
	 * Object private variables
	 */
	private $_url;
	private $_message;
	private $_error;
	
    public function callRequest()
    {
		$params = array(
			'object' => $this,
			'amount' => $this->_getAmount(),
			'order_id' => $this->_getOrderId(),
			'currency_code' => $this->getConfig()->getCurrencyCode($this->_getQuote()->getQuoteCurrencyCode()),
			'customer_id' => $this->_getCustomerId(),
			'customer_email' => $this->_getCustomerEmail(),
			'customer_ip_address' => $this->_getCustomerIpAddress(),
			'payment_means' => $this->getPaymentMeans(),
			'normal_return_url' => $this->getNormalReturnUrl(),
			'cancel_return_url' => $this->getCancelReturnUrl(),
			'automatic_response_url' => $this->getAutomaticReturnUrl(),
			'templatefile' => $this->getConfig()->templatefile,
			'capture_mode' => $this->getConfig()->capture_mode,
			'capture_day' => $this->getConfig()->capture_day,
		);
		
		if ($datafield = $this->getConfig()->getDatafield()) {
			$params['cmd'] = $datafield;
		}
		
		$request = new Mageho_Atos_Model_Api_Request($params);		
		
        if ($request->getError()) {
			$this->_error = true;
	        $this->_message = $request->getDebug();
			
			if ($this->getDebug()->getRequestCmd()) {
				$this->_message.= "\n\n" . $this->getDebug()->getRequestCmd();
			}
		} else {
			$this->_error = false;
	        $this->_url = $request->getUrl();
			$this->_message = $request->getHtml();
		}
    }
	
	public function getUrl() 
	{	
	    return $this->_url;
	}
	
	public function getHtml() 
	{
	    return $this->_message;
	}

    public function getError() 
	{
	    return $this->_error;
	}
	
	public function getPaymentMeans()
	{
	    return explode(',', $this->getConfig()->payment_means);
	}
	
	/**
     * Check whether payment method can be used
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (/*parent::isAvailable($quote) && */$this->getConfig()->isMethodAvailable()) {
            return true;
        }
        
        return false;
    }
	
	/**
     * Config instance getter
     * @return Mageho_Atos_Model_Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $params = array($this->_code);
            if ($store = $this->getStore()) {
                $params[] = is_object($store) ? $store->getId() : $store;
            }
            $this->_config = Mage::getModel('atos/config', $params);
        }
        return $this->_config;
    }
	
	/**
     * Custom getter for payment configuration
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->getConfig()->$field;
    }
	
    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
		
		if ($data->getAtosStandardPaymentMeans()) {
	        $this->getAtosSession()->setAtosStandardPaymentMeans($data->getAtosStandardPaymentMeans());
		}
        return $this;
    }
	
	/**
     * Create main block for standard form
     *
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock($this->_formBlockType, $name)
            ->setMethod($this->getConfig()->getMethodCode())
            ->setPayment($this->getPayment());

        return $block;
    }
	 
    /**
     *  Return URL for cancel payment
	 *
     *  @return	  string Return cancel URL
     */
    public function getCancelReturnUrl()
    {
        return Mage::getUrl('atos/standard/cancel', array('_secure' => true));
    }
	
    /**
     *  Return URL for customer response
     *
     *  @return	  string Return customer URL
     */
    public function getNormalReturnUrl()
    {
        return Mage::getUrl('atos/standard/normal', array('_secure' => true));
    }
	
    /**
     *  Return URL for automatic response
     *
     *  @return	  string Return automatic URL
     */
    public function getAutomaticReturnUrl()
    {
        return Mage::getUrl('atos/automatic/index', array('_secure' => true));
    }
	
    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('atos/standard/redirect', array('_secure' => true));
    }
}