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

class Mageho_Atos_Model_System_Config_Source_Order_Status_New extends Mageho_Atos_Model_System_Config_Source_Order_Status
{
    /* Set null to enable all status */
    protected $_stateStatuses = array(
        Mageho_Sales_Model_Order::STATE_NEW,
        //Mageho_Sales_Model_Order::STATE_PENDING_PAYMENT,
        Mageho_Sales_Model_Order::STATE_PROCESSING,
        //Mageho_Sales_Model_Order::STATE_COMPLETE,
        //Mageho_Sales_Model_Order::STATE_CLOSED,
        //Mageho_Sales_Model_Order::STATE_CANCELED,
        //Mageho_Sales_Model_Order::STATE_HOLDED,
    );
}
