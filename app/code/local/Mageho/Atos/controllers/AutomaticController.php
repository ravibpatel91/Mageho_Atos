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

class Mageho_Atos_AutomaticController extends Mageho_Atos_Controller_Action
{
	public function indexAction() 
	{
		$this->setAtosResponse($_REQUEST);

		/* Log current remote addr */
		if ($this->getConfig()->log_ip_address) {
			Mage::getSingleton('atos/debug')->logRemoteAddr();
		}
		
		/* Check for security enhancement current remote addr with authorized remote addr */
        if ($this->getConfig()->check_ip_address) {
			$remoteAddr = Mage::helper('core/http')->getRemoteAddr(false);
			$allowedIp = $this->getConfig()->getAllowedIp();
			
			if (count($allowedIp)) 
			{
				if (! $this->_isAllowedIP($remoteAddr, $allowedIp)) {
					
		        	Mage::getSingleton('atos/debug')->log(
		        		Mage::helper('atos')->__('%s tries to connect to your server (Authorized Ips : %s).', 
		        			$remoteAddr,
		        			implode(', ', $allowedIp)
		        		)
		        	);
		        	
					$this->_redirect('*/*/failure', array('_secure' => true));
					return;
	      	    }
      	    } else {
	      	    Mage::getSingleton('atos/debug')->log(
	      	    	Mage::helper('atos')->__('You have enabled the verification of the IP address of the server payment but no IP address has been entered.')
	      	    );
      	    }
        }
        
		$order = Mage::getModel('sales/order')->loadByIncrementId($this->getAtosResponse('order_id'));
		if ($order->getId()) {
			$this->saveTransaction($order->getPayment());
			// Update state and status order
			$this->_updateOrderState($order);
		}
	}
	
	/*
	 *
	 * Handled by setAtosResponse function in Controller/Action.php
	 *
	 */
	public function failureAction() 
	{
		$this->_redirect('/');
		return;	
	}
	
    protected function _isAllowedIP($remoteAddr, $authorizedRemoteAddr) 
	{
		$checked = false;
        foreach ($authorizedRemoteAddr as $ip) {
        	if ($remoteAddr == trim($ip)) {
            	$checked = true;
                break;
            }
        }
        return $checked;
    }
}