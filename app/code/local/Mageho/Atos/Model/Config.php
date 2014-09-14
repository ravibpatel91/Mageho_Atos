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
 
class Mageho_Atos_Model_Config extends Mageho_Atos_Model_Abstract
{
    /**
     * Atos Sips Standard
     * @var string
     */
    const METHOD_ATOS_SIPS_PAYMENT_STANDARD = 'atoswps';
	const METHOD_ATOS_SIPS_PAYMENT_SEVERAL = 'atoswpseveral';
	
	/**
     * Current payment method code
     * @var string
     */
    protected $_methodCode = null;

    /**
     * Current store id
     *
     * @var int
     */
    protected $_storeId = null;
	
	/**
     * Set method and store id, if specified
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        if ($params) {
            $method = array_shift($params);
            $this->setMethod($method);
            if ($params) {
                $storeId = array_shift($params);
                $this->setStoreId($storeId);
            }
        }
    }

    /**
     * Method code setter
     *
     * @param string|Mage_Payment_Model_Method_Abstract $method
     * @return Mageho_Atos_Model_Config
     */
    public function setMethod($method)
    {
        if ($method instanceof Mage_Payment_Model_Method_Abstract) {
            $this->_methodCode = $method->getCode();
        } elseif (is_string($method)) {
            $this->_methodCode = $method;
        }
        return $this;
    }

    /**
     * Payment method instance code getter
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    /**
     * Store ID setter
     *
     * @param int $storeId
     * @return Mage_Paypal_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Check whether method active in configuration and supported for merchant country or not
     *
     * @param string $method Method code
     * @return bool
     */
    public function isMethodActive($method)
    {
        if (Mage::getStoreConfigFlag("payment/{$method}/active", $this->_storeId)) {
            return true;
        }
        return false;
    }

    /**
     * Check whether method available for checkout or not
     * Logic based on merchant country, methods dependence
     *
     * @param string $method Method code
     * @return bool
     */
    public function isMethodAvailable($methodCode = null)
    {
        if ($methodCode === null) {
            $methodCode = $this->getMethodCode();
        }

        $result = true;

        if (!$this->isMethodActive($methodCode)) {
            $result = false;
        }

        switch ($methodCode) {
            case self::METHOD_ATOS_SIPS_PAYMENT_STANDARD:
        	case self::METHOD_ATOS_SIPS_PAYMENT_SEVERAL:
                if (! $this->merchant_id || ! $this->bin_request || ! $this->bin_response) {
                    $result = false;
                }
                break;
		}
        return $result;
    }
	
	/**
     * Config field magic getter
     * The specified key can be either in camelCase or under_score format
     * Tries to map specified value according to set payment method code, into the configuration value
     * Sets the values into public class parameters, to avoid redundant calls of this method
     *
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        $underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $key));
        $value = Mage::getStoreConfig($this->_getSpecificConfigPath($underscored), $this->_storeId);
        $this->$key = $value;
        $this->$underscored = $value;
        return $value;
    }
	
	public function getConfigFile($file, $repertory = 'etc')
	{
	    return Mage::getConfig()->getModuleDir($repertory, 'Mageho_Atos') . DS . $file;
	}
	
	/**
     * Get Atos/Sips authorized countries
     *
     * @return array
     */
    public function getMerchantCountries() 
    {
        $countries = array();
        foreach (Mage::getConfig()->getNode('global/payment/atos/merchant_country')->asArray() as $data) {
            $countries[$data['code']] = $data['name'];
        }

        return $countries;
    }
	
	/**
     * Get merchant country code
     *
     * @return string
     */
    public function getMerchantCountry() 
    {
        $countries = Mage::getStoreConfig('general/country');
        $currentCountryCode = strtolower($countries['default']);
        $atosConfigCountries = $this->getMerchantCountries();

        if (count($atosConfigCountries) === 1) {
            return strtolower($atosConfigCountries[0]);
        }

        if (array_key_exists($currentCountryCode, $atosConfigCountries)) {
            $code = array_keys($atosConfigCountries);
            $key = array_search($currentCountryCode, $code);

            return strtolower($code[$key]);
        }

        return 'fr';
    }
	
    /**
     * Récupère un tableau des devises autorisées
     *
     * @return array $currencies
     */
    public function getCurrencies()
    {
		$currencies = array();
        foreach (Mage::getConfig()->getNode('global/payment/atos/currencies')->asArray() as $data) {
            $currencies[$data['iso']] = $data['code'];
        }
        return $currencies;
    }
    
    /**
     * Get currency code
     *
     * @return string|boolean
     */
    public function getCurrencyCode($currentCurrencyCode) 
    {
        $atosConfigCurrencies = $this->getCurrencies();

        if (array_key_exists($currentCurrencyCode, $atosConfigCurrencies)) {
            return $atosConfigCurrencies[$currentCurrencyCode];
        } else {
            return false;
        }
    }

    /**
     * Récupère un tableau des langages autorisées
     *
     * @return array $languages
     */
    public function getLanguages()
	{
        $languages = array();
        foreach (Mage::getConfig()->getNode('global/payment/atos/languages')->asArray() as $data) {
            $languages[$data['code']] = $data['name'];
        }
        return $languages;
	}
	
	/**
     * Get language code
     *
     * @return string
     */
    public function getLanguageCode() 
    {
        $language = substr(Mage::getStoreConfig('general/locale/code'), 0, 2);
        $atosConfigLanguages = $this->getLanguages();

        if (count($atosConfigLanguages) === 1) {
            return strtolower($atosConfigLanguages[0]);
        }

        if (array_key_exists($language, $atosConfigLanguages)) {
            $code = array_keys($atosConfigLanguages);
            $key = array_search($language, $code);

            return strtolower($code[$key]);
        }

        return 'fr';
    }

    /**
     * Récupère un tableau des modes de paiement autorisés
     *
     * @return array $paymentMeans
     */
	public function getPaymentMeans()
    {
        $paymentMeans = array();
        foreach (Mage::getConfig()->getNode('global/payment/atos/payment_means')->asArray() as $data) {
            $paymentMeans[$data['code']] = $data['name'];
        }
        return $paymentMeans;
    }
	
    /**
     * Récupère un tableau des mots clés du champ data 
     *
     * @return array $datafields
     */
	public function getDataFieldKeys()
    {
        $datafields = array();
        foreach (Mage::getConfig()->getNode('global/payment/atos/datafield')->asArray() as $data) {
            $datafields[$data['code']] = $data['name'];
        }
        return $datafields;
    }
	
	public function getDatafield($datafieldToAdd = null)
	{
		$datafield = Mage::getStoreConfig($this->_getSpecificConfigPath('datafield'), $this->_storeId);
		
		if ( ! is_null($datafieldToAdd) ) {
			foreach ($datafieldToAdd as $key => $value) {
				$datafield.= ',' . $key . '=' . $value;
			}
		}
		
		/*
		 * 3D Secure Bypass
		 */
		if ($this->getConfig()->bypass_enabled) 
		{
			$rule = Mage::getModel('salesrule/rule');
			$rule->loadPost(unserialize($this->getConfig()->conditions));
        
			$object = new Varien_Object(array('quote' => $this->_getQuote()));
			
			if ($rule->validate($object)) {
				$datafield.= ',3D_BYPASS';
				// Mage::getSingleton('atos/debug')->log(Mage::helper('atos')->__('3D_BYPASS parameter was added.'));
			} else {
				// Mage::getSingleton('atos/debug')->log(Mage::helper('atos')->__('3D_BYPASS parameter was not added.'));
			}
		}
		
		if ( ! empty($datafield) ) {
			return sprintf(' data=%s', str_replace(',', '\;', $datafield));
		}
	}
	
	/**
     * Get relative credit card image file path
     *
     * @return string
     */
    public function getCardIcon($type)
    {
		$path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'atos' . DS;
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'atos'. DS ;
		$file = Mage::getStoreConfig('atos/images/' . $type . '_icon');
		
		if (! isset($file) && empty($file) || isset($file) && ! file_exists($path . $file)) {
			switch ($type) {
				case 'amex':
					return $url . 'AMEX.gif';
				break;
				case 'aurore':
					return $url . 'AURORE.gif';
				break;
				case 'cb':
					return $url . 'CB.gif';
				break;
				case 'mastercard':
					return $url . 'MASTERCARD.gif';
				break;
				case 'visa':
					return $url . 'VISA.gif';	
				break;
				case 'paylib':
					return $url . 'PAYLIB.gif';
				break;
			}
		} else {
			return $url . $file;
		}
    }
    
    /**
     * Get relative icon file path
     *
     * @return string
     */
    public function getMethodCardIcon()
    {
		$path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'atos' . DS;
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'atos'. DS ;
		$file = $this->icon;
		
		if (! isset($file) && empty($file) || isset($file) && ! file_exists($path . $file)) {
			return false;
		} else {
			return $url . $file;
		}
    }
    
    /**
     * Get authorized IPs
     *
     * @return array
     */
    public function getAllowedIp()
    {
    	$configAllowedIps = unserialize($this->allowed_ip);
    	$ips = array();
    	foreach ($configAllowedIps as $configAllowedIp) {
	    	$ips[] = current($configAllowedIp);
    	}
    	return $ips;
    }
    
    public function isTestMode() 
	{
	    $certificates = Mage::getSingleton('atos/api_files')->getPredefinedCertificates();
        if (isset($certificates[$this->merchant_id])) {
		    return true;
	    }
        return false;
    }
	
	/**
     * Map any supported payment method into a config path by specified field name
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        $path = null;
        switch ($this->_methodCode) {
            case self::METHOD_ATOS_SIPS_PAYMENT_STANDARD:
                $path = $this->_mapStandardFieldset($fieldName);
                break;
			case self::METHOD_ATOS_SIPS_PAYMENT_SEVERAL:
                $path = $this->_mapSeveralFieldset($fieldName);
                break;
        }
        if ($path === null) {
            $path = $this->_mapConfigurationFieldset($fieldName);
        }
        if ($path === null) {
	        $path = $this->_mapSecureCodeFieldset($fieldName);
        }
        if ($path === null) {
            $path = $this->_mapIntegrationFieldset($fieldName);
        }
        if ($path === null) {
            $path = $this->_mapSecurityEnhancementFieldset($fieldName);
        }
        return $path;
    }
	
	/**
     * Map Website Atos Payment Standard Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapStandardFieldset($fieldName)
    {
        if (!$this->_methodCode) {
            return null;
        }
        switch ($fieldName)
        {
            case 'title':
			case 'sort_order':
            case 'order_status':
			case 'invoice_create':
			case 'payment_means':
			case 'order_status_payment_accepted':
			case 'order_status_payment_refused':
			case 'order_status_payment_canceled':
			case 'capture_mode':
			case 'capture_day':
			case 'cms_block':
			case 'icon':
            case 'allowspecific':
            case 'specificcountry':
            case 'min_order_total':
            case 'max_order_total':
                return "atos/{$this->_methodCode}/{$fieldName}";
        	case 'active':
        		return "payment/{$this->_methodCode}/{$fieldName}";
			default:
				return null;
        }
    }
	
	/**
     * Map Website Atos Payment Several Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapSeveralFieldset($fieldName)
    {
        if (!$this->_methodCode) {
            return null;
        }
        switch ($fieldName)
        {
            case 'title':
			case 'sort_order':
            case 'order_status':
			case 'invoice_create':
			case 'payment_means':
			case 'order_status_payment_accepted':
			case 'order_status_payment_refused':
			case 'order_status_payment_canceled':
			case 'nb_payment':
			case 'icon':
			case 'cms_block':
            case 'allowspecific':
            case 'specificcountry':
            case 'min_order_total':
            case 'max_order_total':
                return "atos/{$this->_methodCode}/{$fieldName}";
        	case 'active':
        		return "payment/{$this->_methodCode}/{$fieldName}";
			default:
				return null;
        }
    }
	
	/**
     * Map Configuration Atos Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapConfigurationFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'merchant_id':
                return "atos/configuration/{$fieldName}";
			default:
				return null;
        }
    }
	
	/**
     * Map Integration Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapIntegrationFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'bin_request':
            case 'bin_response':
			case 'pathfile':
			case 'templatefile':
			case 'datafield':
                return "atos/integration/{$fieldName}";
			default:
				return null;
        }
    }
	
	/**
     * Map SecureCode Settings
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapSecureCodeFieldset($fieldName)
    {
        switch ($fieldName)
        {
			case 'bypass_enabled':
			case 'conditions':
                return "atos/securecode/{$fieldName}";
			default:
				return null;
        }
    }
	
	/**
     * Map Security Enhancement During Response Return
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapSecurityEnhancementFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'log_ip_address':
			case 'check_ip_address':
			case 'allowed_ip':
                return "atos/security_enhancement/{$fieldName}";
			default:
				return null;
        }
    }
}