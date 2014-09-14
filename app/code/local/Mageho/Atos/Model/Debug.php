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

class Mageho_Atos_Model_Debug extends Mageho_Atos_Model_Abstract
{	
	const LOG_FILE = 'atos.log';

	public function getRequestCmd()
	{
	    if ($cmd = $this->getAtosSession()->getRequestCmd()) {
		    return $cmd;
		}
	}
	
	public function log($message, $type = Zend_Log::ERR)
	{
		Mage::log($message, $type, self::LOG_FILE);
	}
	
	public function logRemoteAddr()
	{
		$remoteAddr = Mage::helper('core/http')->getRemoteAddr(false);
		$message = Mage::helper('atos')->__('IP registered from automatic response : %s', $remoteAddr);
		
		self::log($message, Zend_Log::INFO);
	}
	
	public function debugFile($file) 
	{
		$path = Mage::getSingleton('atos/api_files')->getLibDir();
		
		if (strpos($file, '.') !== false) {
			$data = explode('.', $file);
			
			switch ($data[0]) {
				case 'pathfile':
					if (! file_exists($path . $file)) {
						return Mage::helper('atos')->__('Impossible to find %s - Check if file exists', $file);
					}
					if (strlen(trim($file)) > 75) {
						return Mage::helper('atos')->__('Chain path pathfile file exceeds 75 characters. It may cause a fatal error.') . ' (' . $file . ')';
					}
					break;
				case 'certif':
					if (! file_exists($path . $file)) {
						return Mage::helper('atos')->__('Impossible to find %s - Check if file exists', $file);
					}
					if (end($data) == 'php') {
						return Mage::helper('atos')->__('Remember to remove php extension of your certificate') . ' (' . $file . ')';
					}
					break;
				case 'request':
				case 'response':
					if (! is_executable($path . $file)) {
						$perms = substr(sprintf('%o', @fileperms($path . $file)), -4);
						return Mage::helper('atos')->__('Impossible to execute %s - Set correct permission on file with your FTP Program (current chmod %s)', $file, $perms);
					}
					break;
			}
		}
		
		return false;
	}
}