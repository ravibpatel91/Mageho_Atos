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

class Mageho_Atos_Block_Adminhtml_System_Config_Field_Path extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$config = Mage::getSingleton('atos/config');
		$debug = Mage::getSingleton('atos/debug');
		$apiFiles = Mage::getSingleton('atos/api_files');
		$atosLib = $apiFiles->getLibDir();
		$atosLibCountChars = strlen(trim($atosLib));
		
		$headerLabel = $this->__('Installation Hint');
					 
		$message = array();
		$message[] = $this->__('%s - %s characters. Keep less than 75 characters for all paths to avoid read error.', $atosLib, $atosLibCountChars);
					 
		if (! function_exists('shell_exec')) {
			$message[] = $this->__('Shell_exec php function not exists or is disabled for security purpose. It must be enabled to activate atos payment method.');
		}
		
		if (PHP_INT_SIZE === 8) {
			$message[] = $this->__('64-bit version of PHP. Use binary files version 64-bit.');
		} elseif (PHP_INT_SIZE === 4) {
			$message[] = $this->__('32-bit version of PHP. Use binary files version 32-bit (it does not imply that the OS and/or Processor is 32-bit).');
		}
		
		if ($atosFiles = $apiFiles->getLibFiles()) {
			foreach ($atosFiles as $file) {
				if ($errors = $debug->debugFile($file)) {
					foreach ($errors as $error) {
						$message[] = $error;
					}
				}
			}
		}
		
		$html = implode('<br />', $message);

$return =<<<RENDER
<h4>{$headerLabel}</h4>
<p>{$this->__('Absolute path to your atos library:')}<kbd>{$atosLib}</kbd></p>
<p><button type="button" class="scalable" onclick="$('atos-debug').toggle(); return false;"><span>{$this->__('Debugging tool')}</span></button></p>
<div id="atos-debug" style="display:none">{$html}</div>
<script>if ($('atos-debug').empty()) $('atos-debug').previous().hide()</script>
<div class="divider">&nbsp;</div>
RENDER;

        return $return;
    }
}