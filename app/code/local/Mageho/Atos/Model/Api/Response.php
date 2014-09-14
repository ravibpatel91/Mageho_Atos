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

class Mageho_Atos_Model_Api_Response extends Mageho_Atos_Model_Config
{
	/* Bank Response Label */
	const BANK_RESPONSE_SUCCESS = 'Transaction approved or successfully treated';
    const BANK_RESPONSE_EXCEEDING_AUTHORIZED = 'Exceeding the authorized ceiling on the map';
    const BANK_RESPONSE_INVALID_MERCHANT_ID = 'Invalid merchant id or e-commerce contract nonexistent';
    const BANK_RESPONSE_KEEP_CREDIT_CARD = 'Keep the credit card';
    const BANK_RESPONSE_SEVERAL_REJECT_ISSUES_POSSIBLE = 'Possible reasons: incorrect validity date, new map not yet initialized by a withdrawal in an ATM service e-carte bleue active ...). Please try again. In case of persistent failure, please contact your bank.';
    const BANK_RESPONSE_KEEP_CREDIT_CARD_SPECIAL_CONDITIONS = 'Keep the credit card, special conditions';
    const BANK_RESPONSE_APPROVED_AFTER_IDENFICATION = 'Approved after identification';
    const BANK_RESPONSE_CHECK_REQUEST_PARAMS = 'Check transfer parameters of the request';
    const BANK_RESPONSE_INVALID_AMOUNT = 'Invalid amount';
    const BANK_RESPONSE_INVALID_NUMBER_CREDIT_CARD_CARDHOLDER = 'Number cardholder invalid credit';
    const BANK_RESPONSE_CREDIT_CARD_ISSUER_UNKNOWN = 'Credit card issuer unknown';
    const BANK_RESPONSE_FORMAT_ERROR = 'Format error';
    const BANK_RESPONSE_UNKNOWN_BUYER_ORGANIZATION = 'Unknown buyer organization';
    const BANK_RESPONSE_EXPIRY_DATE_EXCEEDED = 'Expiry date of the credit card exceeded';
    const BANK_RESPONSE_SUSPECTED_FRAUD = 'Suspected fraud';
    const BANK_RESPONSE_CREDIT_CARD_LOST = 'Lost credit card';
    const BANK_RESPONSE_CREDIT_CARD_STOLEN = 'Stolen credit card';
    const BANK_RESPONSE_INSUFFICIENT_FUNDS_OR_CREDIT_EXCEEDED = 'Insufficient funds or credit exceeded';
    const BANK_RESPONSE_EXPIRY_DATE_INCORRECT = 'Expiry date of the credit card incorrect';
	const BANK_RESPONSE_CREDIT_CARD_MISSING_FILE = 'Credit card missing file';
	const BANK_RESPONSE_UNAUTHORIZED_TRANSACTION_HOLDER = 'Unauthorized transaction such holder';
	const BANK_RESPONSE_PROHIBITED_TRANSACTION_TERMINAL = 'Prohibited transaction terminal';
    const BANK_RESPONSE_ACCEPTOR_CREDIT_CARD_SHOULD_CONTACT_BUYER = 'The acceptor credit card should contact the buyer';
    const BANK_RESPONSE_EXCEEDED_LIMIT_AMOUNT_WITHDRAWAL = ' Exceeded the limit of the amount of withdrawal';
    const BANK_RESPONSE_SAFETY_RULES_NOT_RESPECTED = 'Safety rules are not respected';
    const BANK_RESPONSE_NOT_RECEIVED_RECEIVED_TOO_LATE = 'Response not received or received too late';
    const BANK_RESPONSE_NUMBER_OF_ATTEMPS_EXCEEDED_CREDIT = 'Number of attempts to enter the card number exceeded credit';
    const BANK_RESPONSE_TRY_AGAIN_LATER = 'Please try again later';
    const BANK_RESPONSE_TRANSMITTER_INACCESSIBLE_CARDS = 'Transmitter inaccessible cards';
    const BANK_RESPONSE_TWO_TIMES_TO_CONFIRM_YOUR_PAYMENT = 'If you click two times to confirm your payment: ignore this warning and check your email to see if your order has been confirmed';
    const BANK_RESPONSE_SYSTEM_MALFUNCTION = 'System malfunction';
    const BANK_RESPONSE_DEADLINE_DELAY_GLOBAL_SURVEILLANCE = 'Deadline for the delay global surveillance';
    const BANK_RESPONSE_SERVER_UNAVAILABLE_NETWORK = 'Server unavailable network routing application again';
    const BANK_RESPONSE_INCIDENT_DOMAIN_INITIATOR = 'Incident domain initiator';

	/* Response Label from the bank server */
	const RESPONSE_AUTHORIZATION_ACCEPTED = 'Authorization accepted';
	const RESPONSE_PERMISSION_DENIED = 'Permission denied';
	const RESPONSE_INVALID_TRANSACTION = 'Invalid transaction';
	const RESPONSE_PAYMENT_CANCELED_CUSTOMER = 'Payment canceled by the customer';
	const RESPONSE_FORMAT_ERROR = 'Format error';
	const RESPONSE_NUMBER_ATTEMPTS_CREDIT_CARD_NUMBER_EXCEEDED = 'Number of attempts to enter the card number exceeded';
	const RESPONSE_SERVICE_TEMPORARILY_UNAVAILABLE = 'Service temporarily unavailable';
	const RESPONSE_TRANSACTION_ALREADY_REGISTERED = 'Transaction already registered';
	
	/* CVV Response Label from the bank server */
	const CVV_RESPONSE_INCORRECT = 'Incorrect CVV';
	const CVV_RESPONSE_NO_DATA = 'No data on CVV';
	const CVV_RESPONSE_TREATED = 'Proper control number';
	const CVV_RESPONSE_UNTREATED = 'Number of control untreated';
	const CVV_RESPONSE_MISSING = 'The control number is missing from the request for authorization';
	const CVV_RESPONSE_BANK_NO_CERTIFIED = 'The Bank is not certified, the control has not been made.';
	const CVV_RESPONSE_NO_CVV_ON_CREDIT_CARD = 'No cryptogram on the credit card';
	
	/* Complementary code */
	const COMPLEMENTARY_CODE_ALL_SUCCESS = 'All controls that you joined to are performed successfully';
	const COMPLEMENTARY_CODE_NO_AUTHORIZED_CREDIT = 'The credit card used is not made after the authorized credit';
	const COMPLEMENTARY_CODE_GRAY_LIST = 'The card used belongs to the gray list of merchant';
	const COMPLEMENTARY_CODE_UNREFERENCED_RANGE = 'The bit of the card used belongs to an unreferenced range in the table of binary banking platform';
	const COMPLEMENTARY_CODE_NOT_SAME_NATIONALITY = 'The card number is not in range of the same nationality as the merchant';
	const COMPLEMENTARY_CODE_PROBLEM_ADDITIONAL_LOCAL_CONTROLS = 'The bank server has a problem when processing an additional local controls';
	
	protected $_error;
	protected $_debug;
	
	/*
		API ERROR : Error reading pathfile (no key word F_DEFAULT)
		
		Cette erreur est dûe au chemin d'accès sur les serveurs UNIX qui est limité à 76 caractères.
		Essayer en ligne de commande ln ou remonter le dossier de plusieurs niveaux
		
		Exemple ligne de commande :
		ln -s /home/mondossier /var/www/vhost/mondomaine.fr/httpdocs/lib/atos 
		Ensuite vous pourrez vous servir de /home/mondossier/fichier.txt comme s'il se trouvait dans le dossier /var/www/vhost/mondomaine.fr/httpdocs/lib/atos/fichier.txt. 
	*/
    public function doResponse($data)
    {
		try {
			if (! function_exists('shell_exec')) {
				throw new Exception(Mage::helper('atos')->__("shell_exec php function doesn't exist or is disabled for security purpose."));
			}
			if (! preg_match(':^[a-zA-Z0-9]+$:', $data)) {
            	throw new Exception(Mage::helper('atos')->__('REQUEST DATA not valid. (%s)', $data));
			}
			
			$pathBinResponse = $this->getConfig()->bin_response;

			$command = sprintf('pathfile=%s message=%s', $this->getApiFiles()->getPathfileName(), escapeshellcmd($data));
			
			$response = shell_exec("$pathBinResponse $command 2>&1");
			$hash = $this->hash($response);
			
			
			Mage::log("$pathBinResponse $command 2>&1");
			Mage::log(Zend_Debug::dump($hash, 'atos', false));
        
	
			/* Error from server bank response */
			if ( ! isset($hash['code']) || (isset($response['code']) && $response['code'] != 0) ) {
				throw new Exception(Zend_Debug::dump($hash, 'atos', false));
			}
			
			return $hash;
		} catch (Exception $e) {
			$this->_error = true;
			$this->_debug = $e->getMessage();
			Mage::getSingleton('atos/debug')->log($this->_debug);
			Mage::helper('checkout')->sendPaymentFailedEmail($this->getQuote(), $this->_debug);
		}
    }

	public function hash($response_raw)
	{
	    $response = explode('!', $response_raw);
		
        $hash = array(
			'code' => $response[1],
			'error' => $response[2],
			'merchant_id' => $response[3],
			'merchant_country' => $response[4],
			'amount' => $response[5],
			'transaction_id' => $response[6],
			'payment_means' => $response[7],
			'transmission_date' => $response[8],
			'payment_time' => $response[9],
			'payment_date' => $response[10],
			'response_code' => $response[11],
			'payment_certificate' => $response[12],
			'authorisation_id' => $response[13],
			'currency_code' => $response[14],
			'card_number' => $response[15],
			'cvv_flag' => $response[16],
			'cvv_response_code' => $response[17],
			'bank_response_code' => $response[18],
			'complementary_code' => $response[19],
			'complementary_info' => $response[20],
			'return_context' => $response[21],
			'caddie' => $response[22], // unavailable with NO_RESPONSE_PAGE
			'receipt_complement' => $response[23],
			'merchant_language' => $response[24], // unavailable with NO_RESPONSE_PAGE
			'language' => $response[25],
			'customer_id' => $response[26], // unavailable with NO_RESPONSE_PAGE
			'order_id' => $response[27],
			'customer_email' => $response[28], // unavailable with NO_RESPONSE_PAGE
			'customer_ip_address' => $response[29], // unavailable with NO_RESPONSE_PAGE
			'capture_day' => $response[30],
			'capture_mode' => $response[31],
			'data' => $response[32],
			'response_raw' => $response_raw
		);
		
		if (empty($hash['customer_ip_address'])) {
			$hash['customer_ip_address'] = Mage::helper('core/http')->getRemoteAddr(false);
		}
		
		if (empty($hash['payment_time'])) {
			$hash['payment_time'] = date('H:i:s');
		}
		
		if (empty($hash['payment_date'])) {
			$hash['payment_date'] = date('Y-m-d');
		}
		
		return $hash;
	}
	
	/**
     * Récupère le libellé des codes réponse renvoyés par le serveur bancaire dans le champ response_code 
     * (cf Annexe G du Dictionnaire des données de l'API Sogenactif, Mercanet)
     *
     * @return string
     */
	public function getResponseLabel($code) 
	{
		$hlpr = Mage::helper('atos');
		switch ($code) 
		{
			case '00':
				return $hlpr->__(self::RESPONSE_AUTHORIZATION_ACCEPTED);
			case '02':
			case '03':
			case '05':
			case '34':
				return $hlpr->__(self::RESPONSE_PERMISSION_DENIED);
			case '12':
				return $hlpr->__(self::RESPONSE_INVALID_TRANSACTION);
			case '17':
				return $hlpr->__(self::RESPONSE_PAYMENT_CANCELED_CUSTOMER);
			case '30':
				return $hlpr->__(self::RESPONSE_FORMAT_ERROR);
			case '75':
				return $hlpr->__(self::RESPONSE_NUMBER_ATTEMPTS_CREDIT_CARD_NUMBER_EXCEEDED);
			case '90':
				return $hlpr->__(self::RESPONSE_SERVICE_TEMPORARILY_UNAVAILABLE);
			case '94':
				return $hlpr->__(self::RESPONSE_TRANSACTION_ALREADY_REGISTERED);
			default:
				if (isset($code)) {
					return $code;
				}
				break;
		}
	}

	/**
     * Récupère libellé des codes réponse renvoyés par le serveur Sogenactif dans le champ bank_response_code(cf Annexe F du Dictionnaire des données de l'API Sogenactif)
     *
     * @return string
     */
	public function getBankResponseLabel($code) 
	{
		$hlpr = Mage::helper('atos');
		switch ($code) 
		{
			case '00':	return $hlpr->__(self::BANK_RESPONSE_SUCCESS);
			case '02':	return $hlpr->__(self::BANK_RESPONSE_EXCEEDING_AUTHORIZED);
			case '03':	return $hlpr->__(self::BANK_RESPONSE_INVALID_MERCHANT_ID);
			case '04':	return $hlpr->__(self::BANK_RESPONSE_KEEP_CREDIT_CARD);
			case '05':	return $hlpr->__(self::BANK_RESPONSE_SEVERAL_REJECT_ISSUES_POSSIBLE);
			case '07':	return $hlpr->__(self::BANK_RESPONSE_KEEP_CREDIT_CARD_SPECIAL_CONDITIONS);
			case '08':	return $hlpr->__(self::BANK_RESPONSE_APPROVED_AFTER_IDENFICATION);
			case '12':	return $hlpr->__(self::BANK_RESPONSE_CHECK_REQUEST_PARAMS);
			case '13':	return $hlpr->__(self::BANK_RESPONSE_INVALID_AMOUNT);
			case '14':	return $hlpr->__(self::BANK_RESPONSE_INVALID_NUMBER_CREDIT_CARD_CARDHOLDER);
			case '15':	return $hlpr->__(self::BANK_RESPONSE_CREDIT_CARD_ISSUER_UNKNOWN);
			case '30':	return $hlpr->__(self::BANK_RESPONSE_FORMAT_ERROR);
			case '31':	return $hlpr->__(self::BANK_RESPONSE_UNKNOWN_BUYER_ORGANIZATION);
			case '33':	return $hlpr->__(self::BANK_RESPONSE_EXPIRY_DATE_EXCEEDED);
			case '59':
			case '34':
				return $hlpr->__(self::BANK_RESPONSE_SUSPECTED_FRAUD);
			case '41':	return $hlpr->__(self::BANK_RESPONSE_CREDIT_CARD_LOST);
			case '43':	return $hlpr->__(self::BANK_RESPONSE_CREDIT_CARD_STOLEN);
			case '51':	return $hlpr->__(self::BANK_RESPONSE_INSUFFICIENT_FUNDS_OR_CREDIT_EXCEEDED);
			case '54':	return $hlpr->__(self::BANK_RESPONSE_EXPIRY_DATE_INCORRECT);
			case '56':	return $hlpr->__(self::BANK_RESPONSE_CREDIT_CARD_MISSING_FILE);
			case '57':	return $hlpr->__(self::BANK_RESPONSE_UNAUTHORIZED_TRANSACTION_HOLDER);
			case '58':	return $hlpr->__(self::BANK_RESPONSE_PROHIBITED_TRANSACTION_TERMINAL);
			case '60':	return $hlpr->__(self::BANK_RESPONSE_ACCEPTOR_CREDIT_CARD_SHOULD_CONTACT_BUYER);
			case '61':	return $hlpr->__(self::BANK_RESPONSE_EXCEEDED_LIMIT_AMOUNT_WITHDRAWAL);
			case '63':	return $hlpr->__(self::BANK_RESPONSE_SAFETY_RULES_NOT_RESPECTED);
			case '68':	return $hlpr->__(self::BANK_RESPONSE_NOT_RECEIVED_RECEIVED_TOO_LATE);
			case '75':	return $hlpr->__(self::BANK_RESPONSE_NUMBER_OF_ATTEMPS_EXCEEDED_CREDIT);
			case '90':	return $hlpr->__(self::BANK_RESPONSE_TRY_AGAIN_LATER);
			case '91':	return $hlpr->__(self::BANK_RESPONSE_TRANSMITTER_INACCESSIBLE_CARDS);
			case '94':	return $hlpr->__(self::BANK_RESPONSE_TWO_TIMES_TO_CONFIRM_YOUR_PAYMENT);
			case '96':	return $hlpr->__(self::BANK_RESPONSE_SYSTEM_MALFUNCTION);
			case '97':	return $hlpr->__(self::BANK_RESPONSE_DEADLINE_DELAY_GLOBAL_SURVEILLANCE);
			case '98':	return $hlpr->__(self::BANK_RESPONSE_SERVER_UNAVAILABLE_NETWORK);
			case '99':	return $hlpr->__(self::BANK_RESPONSE_INCIDENT_DOMAIN_INITIATOR);
			default:
				if (isset($code)) {
					return $code;
				}
				break;
		}
	}

	/**
     * Récupère libellé des codes réponse renvoyés par le serveur bancaire dans le champ cvv_response_code 
     * (cf Annexe B du Dictionnaire des données de l'API Sogenactif, Mercanet)
     *
     * @return string
     */
	public function getCvvResponseLabel($code) 
	{
		$hlpr = Mage::helper('atos');
		switch ($code) {
			case '4E':
			case '':
				return $hlpr->__(self::CVV_RESPONSE_INCORRECT);
			case '??':
				return $hlpr->__(self::CVV_RESPONSE_NO_DATA);
			case '4D':
				return $hlpr->__(self::CVV_RESPONSE_TREATED);
			case '50':
				return $hlpr->__(self::CVV_RESPONSE_UNTREATED);
			case '53':
				return $hlpr->__(self::CVV_RESPONSE_MISSING);
			case '55':
				return $hlpr->__(self::CVV_RESPONSE_BANK_NO_CERTIFIED);
			case 'NO':
				return $hlpr->__(self::CVV_RESPONSE_NO_CVV_ON_CREDIT_CARD);
			default:
				if (isset($code)) {
					return $code;
				}
				break;
		}
	}
	
	/**
     * @return string
     */
	public function getComplementaryCode($code)
	{
		$hlpr = Mage::helper('atos');
		switch ($code) 
		{
			case '00':
				return $hlpr->__(self::COMPLEMENTARY_CODE_ALL_SUCCESS);
			case '02':
				return $hlpr->__(self::COMPLEMENTARY_CODE_NO_AUTHORIZED_CREDIT);
			case '03':
				return $hlpr->__(self::COMPLEMENTARY_CODE_GRAY_LIST);
			case '05':
				return $hlpr->__(self::COMPLEMENTARY_CODE_UNREFERENCED_RANGE);
			case '06':
				return $hlpr->__(self::COMPLEMENTARY_CODE_NOT_SAME_NATIONALITY);
			case '99':
				return $hlpr->__(self::COMPLEMENTARY_CODE_PROBLEM_ADDITIONAL_LOCAL_CONTROLS);
			default:
				if (isset($code)) {
					return $code;
				}
				break;
	    }
	}
	
	public function paymentN(array $hash)
	{
		if (isset($hash['data'], $hash['capture_mode']) && $hash['capture_mode'] == 'PAYMENT_N')
		{
			$return = array();
			foreach(explode(';', $hash['data']) as $value) {
				$data = explode('=', $value);
				switch ($data[0]) {
					case 'NB_PAYMENT':
						$return['payment_n'] = $data[1];
					break;
					case 'PERIOD':
						$return['period'] = $data[1];
					break;
					case 'INITIAL_AMOUNT':
						$return['initial_amount'] = $data[1];
					break;
					case 'PAYMENT_DUE_DATES':
						$date = explode(',', $data[1]);
						$return['payment_due_dates'] = array($this->formatDate($date[0]), $this->formatDate($date[1]), $this->formatDate($date[2]));
					break;
				}
			}
			return $return;
		}
	}
	
	public function formatDate($date, $format = 'medium')
	{
		if ($date = explode('/', $date)) {
	    	$newDate = substr($date[0], 0, 4) . '-' . substr($date[0], 4, 2) . '-' . substr($date[0], 6, 2); // as follows : year-month-day
			return Mage::helper('core')->formatDate($newDate, $format, false);
		}
	}
	
	public function formatAmount($amount)
	{
		$int = substr($amount, 0, -2);
		$decimal = substr($amount, -2);
		return Mage::helper('core')->currency($int . '.' . $decimal);
	}
	
	/*
	 * Formate le numéro de carte de crédit 
	 * de la réponse bancaire
	 *
	 * @return string
	 */
	public function getCcNumberEnc($cc)
	{
		if (isset($cc) && !empty($cc)) 
		{
			/* entrée tableau card_number sous la forme 4978.14 */
			// $cc = @explode('.', $response['card_number']);
			
			$cc = preg_split("/\./", $cc);
			if (isset($cc[0], $cc[1])) {
			    return $cc[0] . ' #### #### ##' . $cc[1];
			}
		}
	}
	
	/*
	 * Retourne les quatre derniers numéros 
	 * de la carte de crédit de la réponse bancaire
	 *
	 * @return string
	 */
	public function getCcLast4($cc)
	{
		if (isset($cc) && !empty($cc)) 
		{
			/* entrée tableau card_number sous la forme 4978.14 */
			// $cc = @explode('.', $response['card_number']);
			
			$cc = preg_split("/\./", $cc);
			if (isset($cc[1])) {
			    return '##' . $cc[1];
			}
		}
	}
}