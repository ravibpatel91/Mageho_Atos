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

class Mageho_Atos_Model_System_Config_Source_Capturemode extends Mageho_Atos_Model_Abstract
{
	const PAYMENT_ACTION_CAPTURE = 'AUTHOR_CAPTURE';
	
	/* 
	 * Ce mode de capture est dangereux
	 * Si on oublie de valider la transaction sur le BO de la banque, pas de débit, si supérieur à 7 jours, le débit n'est plus autorisé, la banque fait une nouvelle demande d'autorisation
	 * 
	 * Si activé, ne pas oublier d'enlever le champs depends du fichier system.xml du champs "capture_day" 
	 */
    const PAYMENT_ACTION_AUTHORIZE = 'VALIDATION';

    public function toOptionArray()
    {
        $options = array(
            array('value' => '', 'label' => Mage::helper('atos')->__('Normal')),
            array('value' => self::PAYMENT_ACTION_CAPTURE, 'label' => Mage::helper('atos')->__('Author Capture')),
			array('value' => self::PAYMENT_ACTION_AUTHORIZE, 'label' => Mage::helper('atos')->__('Validation'))
        );
        
        return $options;
    }
}