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
 
class Mageho_Atos_Model_Api_Request extends Mageho_Atos_Model_Config
{
	protected $_url;
	protected $_html;
	protected $_debug;
	protected $_request;
	protected $_error;
	
    public function __construct($params = array())
    {
		self::build($params);
		
		try {
			if (! function_exists('shell_exec')) {
				throw new Exception('shell_exec php function not exists or is disabled for security purpose.');
			}
			
			if (strstr(ini_get('disable_functions'), 'shell_exec')) {
	            throw new Exception('Forbidden `shell_exec` command.');
	        }
	
	        if (! $sipsResult = shell_exec("{$this->getRequest()} &2>1")) {
	            throw new Exception('No result from binary file.');
	        }

			$sipsValues = explode('!', $sipsResult);
		    list(, $code, $error, $html) = $sipsValues;
					
			if (! isset($code, $error, $html)) {
		    	$binRequest = $this->getConfig()->bin_request;
				$binResponse = $this->getConfig()->bin_response;
				$pathfile = $this->getApiFiles()->getPathfileName();
		    
				foreach (array($binRequest, $binResponse, $pathfile) as $file) {
					if ($debug = Mage::getSingleton('atos/debug')->debugFile($file)) {
						throw new Exception($debug);
					}
				}
			}
			
			$debug = Zend_Debug::dump(
				array(
					'error' => $error,
					'code' => $code, 
					'html' => $html,
					'output' => $sipsResult,
					'command' => $this->getRequest()
				), 'atos', false
			);

			if (! isset($code)) {
				throw new Exception($debug);
			}
			
			if ($code == '-1') {
				throw new Exception($debug);
			}
			
			$regs = array();
			preg_match('@<form [^>]*action="([^"]*)"[^>]*>(.*)</form>@i', $html, $regs, PREG_OFFSET_CAPTURE);

			if (! isset($regs[1], $regs[2])) {				
				throw new Exception($debug);
			}
			
			$this->setError(false);
			$this->setUrl($regs[1][0]);
			$this->setHtml($regs[2][0]);
		} catch (Exception $e) {
			$this->setError(true);
			$this->setDebug($e->getMessage());
			Mage::getSingleton('atos/debug')->log($this->getDebug());
			Mage::helper('checkout')->sendPaymentFailedEmail($this->_getQuote(), $this->getDebug());
		}
    }
	
   /*
    *
	* Si les URLs parametrees comporte le parametre ___SID=U
	* le controller redirige vers la page d'accueil
	*
	*/
	public function build($params) 
	{
		
		$request = $this->getConfig()->bin_request;
		$request.= ' pathfile=' . $this->getApiFiles()->getPathfileName();
		$request.= ' language=' . $this->getConfig()->getLanguageCode();
		$request.= ' merchant_id=' . $this->getConfig()->merchant_id;
		$request.= ' merchant_country=' . $this->getConfig()->getMerchantCountry();
		$request.= ' amount=' . $params['amount'];
		$request.= ' currency_code=' . $params['currency_code'];
		$request.= ' normal_return_url=' . str_replace('?___SID=U', '', $params['normal_return_url']);
		$request.= ' cancel_return_url=' . str_replace('?___SID=U', '', $params['cancel_return_url']);
		$request.= ' automatic_response_url=' . str_replace('?___SID=U', '', $params['automatic_response_url']);
		$request.= ' customer_id=' . $params['customer_id'];
		$request.= ' customer_email=' . $params['customer_email'];
		$request.= ' customer_ip_address=' . $params['customer_ip_address'];
		$request.= ' order_id=' . $params['order_id'];
		
		if (! $this->getConfig()->isTestMode())
		{
			if (isset($params['templatefile']) && !empty($params['templatefile'])) {
	            $request.= ' templatefile=' . $params['templatefile'];
			}
			
			if (isset($params['capture_mode'], $params['capture_day']) 
				&& in_array($params['capture_mode'], array('AUTHOR_CAPTURE', 'PAYMENT_N'))
				&& ($params['capture_day'] >= 0 && $params['capture_day'] <= 99)) 
			{
	            $request.= ' capture_mode=' . $params['capture_mode'];
	            $request.= ' capture_day=' . $params['capture_day'];
			}
			
			if (isset($params['cmd'])) {
	            $request.= $params['cmd'];
	        }
	    }
		
		/* Save command line in atos session for debug purpose */
		Mage::getSingleton('atos/session')->setRequestCmd($request);
		$this->setRequest($request);
	}
	
	public function setUrl($url)
	{
		$this->_url = $url;
		return $this;
	}
	
	public function getUrl()
	{
		return $this->_url;	
	}
	
	public function setHtml($html)
	{
		$this->_html = $html;
		return $this;
	}
	
	public function getHtml()
	{
		return $this->_html;
	}
	
	public function setError($bool)
	{
		$this->_error = $bool;
		return $this;
	}
	
	public function getError()
	{
		return $this->_error;
	}
	
	public function setDebug($debug)
	{
		$this->_debug = $debug;
		return $this;
	}
	
	public function getDebug()
	{
		return $this->_debug;
	}
	
	public function setRequest($request)
	{
		$this->_request = $request;
		return $this;
	}
	
	public function getRequest()
	{
		return $this->_request;
	}
}