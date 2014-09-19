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

class Mageho_Atos_Model_System_Config_Source_Merchantid extends Mageho_Atos_Model_Abstract
{
	protected $_options = array();

    public function toOptionArray()
    {
    	if (! count($this->_options)) 
    	{
	    	$predefined = $this->getApiFiles()->getPredefinedCertificates();
	    	foreach ($this->getApiFiles()->getInstalledCertificates() as $certificates) 
	    	{
				$data = explode('.', $certificates);
		        $certificate = $data[count($data)-1];
		        if (ctype_digit($certificate)) 
		        {
		        	if (isset($predefined[$certificate]))
		        	{
			        	$this->_options[] = array(
			        		'value' => $certificate, 
			        		'label' =>  '(' . Mage::helper('atos')->__('Test Account') . ') ' . $predefined[$certificate]
			        	);
		        	} else {
			        	$this->_options[] = array(
			        		'value' => $certificate, 
			        		'label' => $certificate
			        	);
		        	}
	    		}
	    	}
	    }
    	return $this->_options;
    }
    
    public function getMerchantIds() 
    {
    	$merchant_ids = array();
	    foreach ($this->getApiFiles()->getInstalledCertificates() as $certificates) {
			$data = explode('.', $certificates);
		    $certificate = $data[count($data)-1];
		    if (ctype_digit($certificate)) {
		    	$merchant_ids[] = $certificate;
		    }
		}
		return $merchant_ids;
    }
}