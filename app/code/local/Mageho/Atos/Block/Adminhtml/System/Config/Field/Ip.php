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

class Mageho_Atos_Block_Adminhtml_System_Config_Field_Ip extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct() 
    {
        $this->addColumn('ip', array(
            'label' => Mage::helper('atos')->__('Payment server IP address'),
            'style' => 'width:200px'
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('atos')->__('Add new IP address');
        parent::__construct();
    }
}