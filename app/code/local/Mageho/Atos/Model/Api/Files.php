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

class Mageho_Atos_Model_Api_Files extends Mageho_Atos_Model_Config
{
	/* 
	 * Test Certificates 
	 */
	protected static $_predefined = array(
		'013044876511111' => 'eTransaction',
		'014213245611111' => 'Sogenactif',
		'038862749811111' => 'CyberPlus',
		'082584341411111' => 'Mercanet',
		'014141675911111' => 'Scellius',
		'014295303911111' => 'Sherlocks',
		'000000014005555' => 'Aurore Cetelem'
	);

	/*
	 * Get atos lib directory
	 *
	 * @return string
	 */
	public function getLibDir()
	{
	    return Mage::getBaseDir() . DS . 'lib' . DS . 'atos' . DS;
	}
	
	/*
	 * Get all files from atos lib directory
	 *
	 * @return array
	 */
	public function getLibFiles()
	{
		return self::_getLibFiles();
	}
	
    public function getPathfileName()
	{
		$directoryPath = self::getLibDir();
		if ($this->getConfig()->merchant_id) 
		{
			$pathfileFilename = 'pathfile.' . $this->getConfig()->merchant_id;
			if (file_exists($directoryPath . $pathfileFilename)) {
				return $directoryPath . $pathfileFilename;
			} elseif (self::generatePathfile($pathfileFilename)) {
				return $directoryPath . $pathfileFilename;
			}
		} else {
			Mage::getSingleton('atos/debug')->log('I have a problem to get your pathfile name - Check your configured merchant id in your back-office. Do not forget : you must have certificate files in your atos directory.');
			return;	
		}
    }
    
    public function generatePathfile($pathfileFilename)
    {
		$directoryPath = self::getLibDir();
		
		$io = new Varien_Io_File();
		$io->setAllowCreateFolders(true);
		$io->open(array('path' => $directoryPath));
			
		$template = new Zend_Config_Xml($this->getConfigFile('template.xml'));
		$pathfileContent = $template->pathfile->content;
			
		$pathfileContent = str_replace('{{mediaAtosPath}}', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'atos' . DS, $pathfileContent);
		$pathfileContent = str_replace('{{certificateAtosPath}}', $directoryPath . 'certif', $pathfileContent);
		$pathfileContent = str_replace('{{parmcomAtosPath}}', $directoryPath . 'parmcom', $pathfileContent);
		$pathfileContent = str_replace('{{pathfileAtosPath}}', $directoryPath . $pathfileFilename, $pathfileContent);
			
		$io->streamOpen($pathfileFilename);
		$io->streamWrite($pathfileContent);
		$io->streamClose();
		
		/*
		 *
		 * Generate parcom file
		 *
		 */
		
		$data = explode('.', $pathfileFilename);
		$merchant_id = $data[count($data)-1];
		$parcomFilename = 'parmcom.' . $merchant_id;
		
		if (! file_exists($directoryPath . $parcomFilename)) 
		{
			$parcomContent = $template->parcom->content;
			
			$parcomContent = str_replace('{{autoResponseUrl}}', Mage::getModel('atos/method_standard')->getAutomaticReturnUrl(), $parcomContent);
			$parcomContent = str_replace('{{cancelUrl}}', Mage::getModel('atos/method_standard')->getCancelReturnUrl(), $parcomContent);
			$parcomContent = str_replace('{{returnUrl}}', Mage::getModel('atos/method_standard')->getNormalReturnUrl(), $parcomContent);
			$parcomContent = str_replace('{{cardList}}', implode(',', Mage::getModel('atos/system_config_source_paymentmeans')->getCCValues()), $parcomContent);
			$parcomContent = str_replace('{{currency}}', Mage::getModel('atos/config')->getCurrencyCode(Mage::app()->getStore()->getCurrentCurrencyCode()), $parcomContent);
			$parcomContent = str_replace('{{language}}', Mage::getModel('atos/config')->getLanguageCode(), $parcomContent);
			$parcomContent = str_replace('{{merchantCountry}}', Mage::getModel('atos/config')->getMerchantCountry(), $parcomContent);
			$parcomContent = str_replace('{{merchantLanguage}}', Mage::getModel('atos/config')->getLanguageCode(), $parcomContent);
			$parcomContent = str_replace('{{paymentMeans}}', implode(',2,', Mage::getModel('atos/system_config_source_paymentmeans')->getCCValues()) . ',2', $parcomContent);
			$parcomContent = str_replace('{{templateFilename}}', $this->getConfig()->templatefile, $parcomContent);
			
			$io->streamOpen($parcomFilename);
			$io->streamWrite($parcomContent);
			$io->streamClose();
        }
    }

	public function getCertificate()
	{
        $certificate = null;
	    foreach (self::getInstalledCertificates() as $current) 
	    {
	        if (! isset($current['test'])) {
	            return $current;
	        }
	        if (! isset($certificate)) {
		        $certificate = $current;
		    }
        }
        return $certificate;
	}
	
    public function getInstalledCertificates()
	{
		$certificates = self::_getLibFiles('certif');
		if (empty($certificates)) {
			$message = Mage::helper('atos')->__('No certificates found');
			$certificates[] = $message;
			Mage::getSingleton('atos/debug')->log($message);
		}
	    return $certificates;
    }
	
    public function getPredefinedCertificates()
	{
		return self::$_predefined;
    }
	
    public function getInstalledParmcom() 
	{
		$parmcom = self::_getLibFiles('parmcom');
        if (empty($parmcom)) {
			Mage::getSingleton('atos/debug')->log("Parcom files doesn't exist");
        	return false;
		}
        return $parmcom;
    }
    
    /*
     * List of files from atos lib directory
     *
     * @options $name
     * @return array
     */
    private function _getLibFiles($filename = '')
    {
	    $directoryPath = self::getLibDir();
		
		if (! is_dir($directoryPath)) {
			Mage::getSingleton('atos/debug')->log($directoryPath . ' is not a directory');
        	return false;
		}

        $iter = new DirectoryIterator($directoryPath); 
        $files = array();
		foreach($iter as $file) 
		{
        	if ($file->isDot()) {
        		continue;
        	}
        	$data = explode('.', $file->getFilename());
        	if ($filename != '') {
	        	if ($filename == $data[0]) {
		        	$files[] = $file->getFilename();
	        	} else {
		        	continue;
	        	}
        	} else {
				$files[] = $file->getFilename();
			}
        }
        return $files;
    }
}