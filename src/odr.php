<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ .'/helpers.php';

defined('WHMCS_ODR_HOST_LIVE') || define('WHMCS_ODR_HOST_LIVE', 'https://www.opendomainregistry.net/');
defined('WHMCS_ODR_HOST_TEST') || define('WHMCS_ODR_HOST_TEST', 'http://odrregistry.nl/');

function odr_getConfigArray()
{
    $liveHost = WHMCS_ODR_HOST_LIVE;
    $testHost = WHMCS_ODR_HOST_TEST;

    return array(
        'FriendlyName' => array(
            'Type'  => 'System',
            'Value' => 'OpenDomainRegistry',
        ),

        'Description' => array(
            'Type'  => 'System',
            'Value' => 'Register, update and transfer domains, supported over 480 TLD, buy and renew SSL certificates, reseller support and many more. Visit <a href="' . $liveHost . '">' . $liveHost . '</a> for more details',
        ),

        'ApiKey'         => array(
            'FriendlyName' => 'API Key',
            'Type'         => 'text',
            'Size'         => '50',
            'Required'     => true,
            'Description'  => "Enter your API key here."
                . " To find your API key, login to Open Domain Registry ({$liveHost}) and click on 'API Keys'/'API sleutel' in the main menu."
                . " In case you want to generate new API keys or change current ones, just click on 'Renew API keys'.",
        ),

        'ApiSecret'      => array(
            'FriendlyName' => 'API Secret',
            'Type'         => 'password',
            'Size'         => '50',
            'Required'     => true,
            'Description'  => "Enter your API secret here."
                . " To find your API secret, login to Open Domain Registry ({$liveHost}) and click on 'API Keys'/'API sleutel' in the main menu."
                . " In case you want to generate new API keys or change current ones, just click on 'Renew API keys'.",
        ),

        'TestApiKey'         => array(
            'FriendlyName' => 'API Key for Testmode',
            'Type'         => 'text',
            'Size'         => '50',
            'Description'  => "Enter your API key for Testmode here."
                . " To find your API key, login to Open Domain Registry Staging Environment ({$testHost}) and click on 'API Keys'/'API sleutel' in the main menu."
                . " In case you want to generate new API keys or change current ones, just click on 'Renew API keys'.",
        ),

        'TestApiSecret'      => array(
            'FriendlyName' => 'API Secret for Testmode',
            'Type'         => 'password',
            'Size'         => '50',
            'Description'  => "Enter your API secret for Testmode here."
                . " To find your API secret, login to Open Domain Registry Staging Environment ({$testHost}) and click on 'API Keys'/'API sleutel' in the main menu."
                . " In case you want to generate new API keys or change current ones, just click on 'Renew API keys'.",
        ),

        'Testmode'       => array(
            'FriendlyName' => 'Test Mode',
            'Type'         => 'yesno',
            'Default'      => false,
        ),

        'EnableLogs'       => array(
            'FriendlyName' => 'Enable testing logs',
            'Type'         => 'yesno',
            'Default'      => false,
            'Description'  => "Enables log writing for debugging purposes."
                . " Before enabling, please, be sure to create '/log/' folder (or value from 'logsPath') and set write permissions for it.",
        ),

        'logsPath'       => array(
            'FriendlyName' => 'Enable testing logs',
            'Type'         => 'text',
            'Size'         => 50,
            'Default'      => __DIR__ . '/log/api' . date('Ymd_H') .'.log',
            'Description'  => "Pick custom log path."
                . " If logs are disable, this option will be ignored"
                . " You can use following replacements: #DAY#, #MONTH#, #YEAR#, #HOUR#",
        ),
    );
}

/**
 * @param Api_Odr $module
 *
 * @return string
 */
function odr_Login($module)
{
    return Odr_Whmcs::login($module);
}

/**
 * @param array $params
 *
 * @return Api_Odr
 */
function odr_Config($params)
{
    return Odr_Whmcs::getModule($params);
}

/**
 * Returns array of nameservers
 *
 * @param array $params
 *
 * @return array|string
 */
function odr_GetNameservers($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    $resp = $result['response'];

    $i = 1;

    foreach ($resp['nameservers'] as $ns) {
        $values['ns' . $i] = $ns;

        $i++;
    }

    return $values;
}

function odr_SaveNameservers($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    $resp = $result['response'];

    $data = Odr_Whmcs::prepareDomainData($params, $resp['contacts_map']['REGISTRANT'], $resp['contacts_map']['ONSITE'], empty($resp['contacts_map']['TECH']) ? $resp['contacts_map']['ONSITE'] : $resp['contacts_map']['TECH']);

    try {
        $result = $module->updateDomain($params['domainname'], $data)->getResult();
    } catch (Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    return $values;
}

function odr_RegisterDomain($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    $contactId = Odr_Whmcs::obtainContact($module, $params);

    if (!empty($contactId['error'])) {
        return $contactId;
    }

    $domainData = Odr_Whmcs::prepareDomainData($params, $contactId);

    try {
        $result = $module->registerDomain($params['domainname'], $domainData)->getResult();
    } catch (Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    return $values;
}

function odr_TransferDomain($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    $contactId = Odr_Whmcs::obtainContact($module, $params);

    if (!empty($contactId['error'])) {
        return $contactId;
    }

    $domainData = \Odr_Whmcs::prepareDomainData($params, $contactId);

    try {
        $result = $module->transferDomain($params['domainname'], $domainData)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    return $values;
}

function odr_RenewDomain($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    if ($result['response']['status'] !== 'QUARANTINE') {
        if ($result['response']['autorenew'] === 'OFF') {
            $values['error'] = 'Renewal is impossible';
        } else {
            $values['status'] = 'Domain Renewed';
        }

        return $values;
    }

    $reactivate = $module->reactivateDomain($params['domainname'])->getResult();

    if ($reactivate['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($reactivate['response']) ? $reactivate['response']['message'] : $reactivate['response']);

        return $values;
    }

    if (!$reactivate['response']['result']) {
        $values['error'] = 'Reactivation is impossible';

        return $values;
    }

    $values['status'] = 'Domain Reactivated';

    return $values;
}

function odr_GetEPPCode($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainAuthCode($params['domainname'])->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    if (!empty($result['response']['auth_code'])) {
        if (is_string($result['response']['auth_code'])) {
            $values['eppcode'] = $result['response']['auth_code'];
        } else {
            $values['eppcode'] = 'EPP code was sent to domain owner email address';
        }
    } else {
        $values['error'] = 'Either EPP code not supported or it was sent to domain owner email address';
    }

    return $values;
}

function odr_TransferSync($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomain($params['domainname'])->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    if ($result['response']['status'] === 'REGISTERED') {
        $values['completed']  = true;
        $values['expirydate'] = $result['response']['expiration_date'];
    } elseif ($result['response']['status'] === 'PENDING') {
        $values['error'] = 'Domain ' . $params['domainname'] . ' is still in the following status: ' . $result['response']['status'];
    } else {
        $values['failed'] = true;
        $values['reason'] = 'Domain ' . $params['domainname'] . ' is currently in the following status: ' . $result['response']['status'];
    }

    return $values;
}

function odr_Sync($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    if (empty($params['domainname']) && !empty($params['domain'])) {
        $params['domainname'] = $params['domain'];
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = '[ERROR] Sync contact | Error retrieving domain details ODR for ' . $params['domainname'];

        return $values;
    }

    $domain = $result['response'];

    // Sync domain status
    if ($domain['status'] === 'REGISTERED') {
        $values['active'] = true;
    } elseif ($domain['status'] === 'DELETED' || $domain['status'] === 'QUARANTINE') {
        $values['expired'] = true;
    }

    $values['expirydate'] = $domain['expiration_date'];

    return $values;
}

function odr_RequestDelete($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->setDomainAutorenewOff($params['domainname'])->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    if (!$result['response']['result']) {
        $values['error'] = 'Domain deletion already scheduled';

        return $values;
    }

    $values['domainid'] = $params['domainid'];
    $values['status']   = 'Cancelled';

    return $values;
}

class Odr_Whmcs
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';

    const URL_TEST = 'http://api.odrregistry.nl';
    const URL_LIVE = Api_Odr::URL;

    const MODULE = 'odr';

    const LEGAL_FORM_COMPANY = 'ANDERS';
    const LEGAL_FORM_PERSON  = 'PERSOON';

    /**
     * @var null|\Api_Odr
     *
     * @static
     */
    static public $module;

    static public function searchContact($name, array $contacts)
    {
        foreach ($contacts as $val) {
            if (strpos($val['name'], '[')) {
                $named = explode('[', $val['name'], 2);

                $val['name'] = trim($named[0]);
            }

            if ($name === $val['name']) {
                return $val['id'];
            }
        }

        return null;
    }

    static public function formatContactName($firstName, $lastName, $companyName = '')
    {
        $names = array(
            trim($firstName),
            trim($lastName),
        );

        if (trim($companyName)) {
            $names[] = '(' . trim($companyName) . ')';
        }

        return implode(' ', array_filter($names));
    }

    static public function getInitials($name)
    {
        $initials = mb_substr($name, 0, 1);

        return mb_strtoupper($initials);
    }

    static public function getInitialsFull($name)
    {
        $initials = '';
        $exploded = explode(' ', $name);

        foreach ($exploded as $letter) {
            $initials .= mb_substr($letter, 0, 1);
        }

        return mb_strtoupper($initials);
    }

    static public function getCountries()
    {
        return array(
            'nl' => array(
                'name'  => 'Netherlands',
                'code'  => 'NL',
                'phone' => '+31',
            ),

            'be' => array(
                'name'  => 'Belgium',
                'code'  => 'BE',
                'phone' => '+32',
            ),

            'es' => array(
                'name'  => 'Spain',
                'code'  => 'ES',
                'phone' => '+34',
            ),

            'fr' => array(
                'name'  => 'France',
                'code'  => 'FR',
                'phone' => '+33',
            ),

            'de' => array(
                'name'  => 'Germany',
                'code'  => 'de',
                'phone' => '+44',
            ),

            'cy' => array(
                'name'  => 'Cyprus',
                'code'  => 'CY',
                'phone' => '+357',
            ),

            'lu' => array(
                'name'  => 'Luxembourg',
                'code'  => 'LU',
                'phone' => '+352',
            ),

            'cw' => array(
                'name'  => 'Curacao',
                'code'  => 'CW',
                'phone' => '+599',
            ),

            'sc' => array(
                'name'  => 'Seychelles',
                'code'  => 'SC',
                'phone' => '+248',
            ),

            'ca' => array(
                'name'  => 'Canada',
                'code'  => 'CA',
                'phone' => '+1',
            ),

            'hk' => array(
                'name'  => 'Hong Kong',
                'code'  => 'HK',
                'phone' => '+852',
            ),

            'uk' => array(
                'name'  => 'United Kingdom',
                'code'  => 'UK',
                'phone' => '+44',
            ),

            'us' => array(
                'name'  => 'United States',
                'code'  => 'US',
                'phone' => '+1',
            ),

            'ru' => array(
                'name'  => 'Russian Federation',
                'code'  => 'RU',
                'phone' => '+7',
            ),
        );
    }

    static public function getCountry($country)
    {
        $countries = self::getCountries();

        $country = strtolower($country);

        if (!array_key_exists($country, $countries)) {
            $country = 'nl';
        }

        return $countries[$country];
    }

    static public function formatPhone($country, $phone)
    {
        if ($country[0] !== '+') {
            $country = self::getCountry($country);

            $country = $country['phone'];
        }

        return $country . '.' . $phone;
    }

    /**
     * Returns correct legal form
     *
     * @param null|string $companyName Company name. If present, we think it's company. If not - it's person
     *
     * @return string
     *
     * @static
     */
    static public function getLegalForm($companyName = null)
    {
        return $companyName ? self::LEGAL_FORM_COMPANY : self::LEGAL_FORM_PERSON;
    }

    /**
     * Splits address correctly
     *
     * @param string $street Whole address as one string
     *
     * @return null|array
     *
     * @static
     */
    static public function splitStreet($street)
    {
        preg_match('/^(\d*[\p{L}äöüß\d \'\-\.]+)[,\s]+(\d+)\s*([\p{L}äöüß\d\-\/]*)$/u', trim($street), $array);

        if (empty($array[1])) {
            return null;
        }

        $result = array(
            'street'       => trim($array[1]),
            'house_number' => empty($array[2]) ? '1' : trim($array[2]),
            'additional'   => empty($array[3]) ? null : trim($array[3], ' -/\\'),
        );

        return $result;
    }

    /**
     * Prepares company (a. k. a., full name) name
     *
     * @param string $companyName Company name. Can be empty
     * @param string $fullName    Combination of first, middle and last names
     *
     * @return string
     *
     * @static
     */
    static public function getCompanyName($companyName, $fullName)
    {
        return $companyName ?: $fullName;
    }

    /**
     * Prepares correct postal code
     *
     * @param string $postCode Source postal code
     *
     * @return string
     *
     * @static
     */
    static public function getPostalCode($postCode)
    {
        return strtoupper(str_replace(' ', '', $postCode));
    }

    /**
     * Prepares clear phone number
     *
     * @param string $phone Source phone number
     *
     * @return string
     *
     * @static
     */
    static public function getPhone($phone)
    {
        $phone = trim($phone, ' .');

        if (strpos($phone, '.')) {
            list(, $phone) = explode('.', $phone, 2);
        }

        $r = array(
            '+' => '',
            '.' => '',
            '(' => '',
            ')' => '',
            ' ' => '',
            '-' => '',
        );

        return trim(str_replace(array_keys($r), array_values($r), $phone));
    }

    /**
     * Converts WHMCS data to ODR format
     *
     * @param array $whmcs Source data
     *
     * @return array|string|null Formatted data
     *
     * @static
     */
    static public function contactDataToOdr(array $whmcs)
    {
        $street = self::splitStreet($whmcs['address1']);

        if ($street === null) {
            return 'Invalid address';
        }

        $country     = self::getCountry($whmcs['country']);
        $phone       = self::getPhone($whmcs['phonenumber']);
        $zip         = self::getPostalCode($whmcs['postcode']);
        $initials    = self::getInitials($whmcs['firstname']);
        $legalForm   = self::getLegalForm($whmcs['companyname']);
        $fullName    = self::formatContactName($whmcs['firstname'], $whmcs['lastname'], $whmcs['companyname']);
        $companyName = self::getCompanyName($whmcs['companyname'], $fullName);

        $houseNumber = $street['house_number'] . $street['additional'];

        return array(
            'first_name'   => $whmcs['firstname'],
            'middle_name'  => '',
            'last_name'    => $whmcs['lastname'],
            'full_name'    => $fullName,
            'initials'     => $initials,
            'gender'       => 'NA',
            'city'         => $whmcs['city'],
            'language'     => $country['code'],
            'email'        => $whmcs['email'],
            'country'      => $country['code'],
            'state'        => $whmcs['state'],
            'street'       => $street['street'],
            'house_number' => $houseNumber,
            'phone'        => self::formatPhone($country['phone'], $phone),
            'fax'          => null,
            'postal_code'  => $zip,

            'organization_legal_form' => $legalForm,

            'company_name'         => $companyName,
            'company_city'         => $whmcs['city'],
            'company_email'        => $whmcs['email'],
            'company_phone'        => self::formatPhone($country['phone'], $phone),
            'company_fax'          => null,
            'company_postal_code'  => $zip,
            'company_street'       => $street['street'],
            'company_house_number' => $houseNumber,
        );
    }

    static public function getModule(array $params)
    {
        if (self::$module) {
            return self::$module;
        }

        $isTestmode = false;

        if (!empty($params['Testmode'])) {
            $isTestmode = true;
        }

        $module = array(
            'api_key'     => $isTestmode && $params['TestApiKey']    ? $params['TestApiKey']    : $params['ApiKey'],
            'api_secret'  => $isTestmode && $params['TestApiSecret'] ? $params['TestApiSecret'] : $params['ApiSecret'],
            'url'         => $isTestmode ? Odr_Whmcs::URL_TEST : Odr_Whmcs::URL_LIVE,
            'enable_logs' => !empty($params['EnableLogs']),
            'logs_path'   => empty($params['LogsPath']) ? __DIR__ . '/log/api' . date('Ymd_H') .'.log' : $params['LogsPath'],
        );

        return new Api_Odr($module);
    }

    /**
     * Checks if login already performs and if not (or stored authentication details expired), it logins again
     *
     * @param \Api_Odr $module Module instance
     *
     * @return array|bool
     *
     * @throws \Api_Odr_Exception
     */
    static public function login(Api_Odr $module)
    {
        $sessionKey      = self::getSessionKey();
        $loginExpiration = 3600; // 60 * 60

        if (!empty($_SESSION[$sessionKey]) && !empty($_SESSION[$sessionKey]['auth_name']) && !empty($_SESSION[$sessionKey]['auth_value']) && $_SESSION[$sessionKey]['expiration_at'] > time()) {
            $module->setHeader($_SESSION[$sessionKey]['auth_name'], $_SESSION[$sessionKey]['auth_value']);

            return true;
        }

        try {
            $result = $module->login()->getResult();
        } catch (\Exception $e) {
            return array(
                'status' => self::STATUS_ERROR,
                'error'  => 'Can\'t login, reason - ' . $e->getMessage(),
            );
        }

        if ($result['status'] === Api_Odr::STATUS_ERROR) {
            return array(
                'status' => self::STATUS_ERROR,
                'error'  => 'Can\'t login, reason - ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
            );
        }

        $_SESSION[$sessionKey] = array(
            'auth_name'     => $result['response']['as_header'],
            'auth_value'    => $result['response']['token'],
            'expiration_at' => time() + $loginExpiration,
        );

        return true;
    }

    static public function getSessionKey()
    {
        return self::MODULE . '_loginState';
    }

    static public function reformatContact(array $contact)
    {
        foreach (self::getUselessContactData() as $key) {
            if (array_key_exists($key, $contact)) {
                unset($contact[$key]);
            }
        }

        ksort($contact);

        return $contact;
    }

    static public function getUselessContactData()
    {
        return array(
            'id',
            'customer_id',
            'comment',
            'created',
            'updated',
            'is_filled',
            'birthday',
            'url',
            'company_url',
            'company_vatin',
            'company_kvk',
            'company_address',
        );
    }

    static public function obtainContact(Api_Odr $module, array $params)
    {
        $values = array();

        try {
            $result = $module->getContacts()->getResult();
        } catch (\Exception $e) {
            $values['error'] = 'Following error occurred: ' . $e->getMessage();

            return $values;
        }

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

            return $values;
        }

        $contactId = Odr_Whmcs::searchContact(Odr_Whmcs::formatContactName($params['firstname'], $params['lastname'], $params['companyname']), $result['response']);

        // Contact doesn't exist, create a new
        if ($contactId === null) {
            $contactData = Odr_Whmcs::contactDataToOdr($params);

            if ($contactData === null || is_string($contactData)) {
                $values['error'] = 'Following error occurred: ' . (is_string($contactData) ? $contactData : 'Contact creation is impossible, due to corrupted data');

                return $values;
            }

            try {
                $result = $module->createContact($contactData)->getResult();
            } catch (\Exception $e) {
                $values['error'] = 'Following error occurred: ' . $e->getMessage();

                return $values;
            }

            if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
                $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

                return $values;
            }

            $contactId = empty($result['response']['data']['id']) ? null : $result['response']['data']['id'];
        }

        if ($contactId === null) {
            $values['error'] = 'Contact creation was not successful, please either try using different data or contact support';

            return $values;
        }

        return $contactId;
    }

    static public function prepareDomainData(array $params, $contactRegistrant, $contactOnsite = null, $contactTech = null)
    {
        if ($contactOnsite === null) {
            $contactOnsite = $contactRegistrant;
        }

        if ($contactTech === null) {
            $contactTech = $contactRegistrant;
        }

        $domain = array();

        if (!empty($params['transfersecret'])) {
            $domain['auth_code'] = $params['transfersecret'];
        }

        $domain['period']             = $params['regperiod'];
        $domain['contact_registrant'] = $contactRegistrant;
        $domain['contact_onsite']     = $contactOnsite;
        $domain['contact_tech']       = $contactTech;
        $domain['ns1']['host']        = $params['ns1'];
        $domain['ns2']['host']        = $params['ns2'];

        $k = 3;

        for ($i = $k; $i <= 5; $i++) {
            if (!empty($params['ns' . $i])) {
                $domain['ns' . $k] = $params['ns' . $k];

                $k++;
            }
        }

        return $domain;
    }
}