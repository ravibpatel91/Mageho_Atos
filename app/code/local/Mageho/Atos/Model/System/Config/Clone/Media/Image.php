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

class Mageho_Atos_Model_System_Config_Clone_Media_Image 
	extends Mage_Adminhtml_Model_System_Config_Clone_Media_Image
{
    /**
     * Get fields prefixes
     *
     * @return array
     */
    public function getPrefixes()
    {
		$paymentMeans = Mage::getSingleton('atos/config')->getPaymentMeans();
        $prefixes = array();
        foreach ($paymentMeans as $key => $value) {
            $prefixes[] = array(
                'field' => strtolower($key) . '_',
                'label' => $value,
            );
        }
        return $prefixes;
    }
}