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

class Mageho_Atos_Model_System_Config_Source_Paymentmeansforseveralpayment extends Mageho_Atos_Model_Abstract
{
    public function toOptionArray()
    {
        $options =  array();
        foreach (Mage::getSingleton('atos/config')->getPaymentMeans() as $code => $name) 
		{
			/*
			 * Cartes bancaires suppportées pour le paiement en plusieurs fois
			 */
			if (in_array($code, array('CB', 'VISA', 'MASTERCARD'))) {
        		$options[] = array('value' => $code, 'label' => $name);
			}
        }
        return $options;
    }
}