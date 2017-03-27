<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;

require_once __DIR__ .'/helpers.php';

defined('WHMCS_ODR_HOST_LIVE') || define('WHMCS_ODR_HOST_LIVE', 'https://www.opendomainregistry.net/');
defined('WHMCS_ODR_HOST_TEST') || define('WHMCS_ODR_HOST_TEST', 'http://odrregistry.nl/');

function odr_MetaData()
{
    return array(
        'DisplayName' => 'OpenDomainRegistry',
        'APIVersion'  => '3.1',
    );
}

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
            'Value' => 'Register, update and transfer domains, supported over 480 TLD, buy and renew SSL certificates, reseller support and many more. Visit <a href="' . $liveHost . '">' . $liveHost . '</a> for more details<br>'
                . 'For support, please <a href="https://github.com/opendomainregistry/whmcs_module/issues/new" target="_blank">create issue</a> at <a href="https://github.com/opendomainregistry/whmcs_module" target="_blank">Github</a>.',
        ),

        'OdrApiKey'         => array(
            'FriendlyName' => 'API Key',
            'Type'         => 'text',
            'Size'         => '50',
            'Required'     => true,
            'Description'  => 'Enter your API key here.<br>'
                . 'To find your API key, login to Open Domain Registry (' . $liveHost . ') and click on "API Keys"/"API sleutel" in the main menu.<br>'
                . 'In case you want to generate new API keys or change current ones, just click on "Renew API keys".',
        ),

        'OdrApiSecret'      => array(
            'FriendlyName' => 'API Secret',
            'Type'         => 'password',
            'Size'         => '50',
            'Required'     => true,
            'Description'  => 'Enter your API secret here.<br>'
                . 'To find your API secret, login to Open Domain Registry (' . $liveHost . ') and click on "API Keys"/"API sleutel" in the main menu.<br>'
                . 'In case you want to generate new API keys or change current ones, just click on "Renew API keys".',
        ),

        'OdrTestApiKey'         => array(
            'FriendlyName' => 'API Key for Testmode',
            'Type'         => 'text',
            'Size'         => '50',
            'Description'  => 'Enter your API key for Testmode here.<br>'
                . 'To find your API key, login to Open Domain Registry Staging Environment (' . $testHost . ') and click on "API Keys"/"API sleutel" in the main menu.<br>'
                . 'In case you want to generate new API keys or change current ones, just click on "Renew API keys".',
        ),

        'OdrTestApiSecret'      => array(
            'FriendlyName' => 'API Secret for Testmode',
            'Type'         => 'password',
            'Size'         => '50',
            'Description'  => 'Enter your API secret for Testmode here.<br>'
                . 'To find your API secret, login to Open Domain Registry Staging Environment (' . $testHost . ') and click on "API Keys"/"API sleutel" in the main menu.<br>'
                . 'In case you want to generate new API keys or change current ones, just click on "Renew API keys".',
        ),

        'OdrTestmode'       => array(
            'FriendlyName' => 'Test Mode',
            'Type'         => 'yesno',
            'Description'  => 'Enable Test Mode or not.<br>'
                . 'Warning! When Test Mode enabled, requests will be sent to a different URL (' . $testHost . ') and different API keys will be used. Be sure you\'re registered there and user have generated API keys',
            'Default'      => false,
        ),

        'OdrEnableLogs'       => array(
            'FriendlyName' => 'Enable testing logs',
            'Type'         => 'yesno',
            'Default'      => false,
            'Description'  => 'Enables log writing for debugging purposes.<br>'
                . 'Before enabling, please, be sure to create "/log/" folder (or value from "OdrLogsPath") and set write permissions for it.<br>'
                . 'If you encounter any issue, we ask you to send us latest logs, that way we can find and fix the issue much easier.',
        ),

        'OdrLogsPath'       => array(
            'FriendlyName' => 'Debug log file path',
            'Type'         => 'text',
            'Size'         => '120',
            'Default'      => __DIR__ . '/log/api#YEAR##MONTH##DAY#_#HOUR#.log',
            'Description'  => 'Pick custom log file path.<br>'
                . 'If logs are disable, this option will be ignored.<br>'
                . 'You can use following replacements: <strong>#DAY#</strong>, <strong>#MONTH#</strong>, <strong>#YEAR#</strong>, <strong>#HOUR#</strong>',
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

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response'])
        );
    }

    $resp = $result['response'];

    $i = 1;

    $nameservers = array();

    foreach ($resp['nameservers'] as $ns) {
        $nameservers['ns' . $i] = $ns;

        $i++;
    }

    return $nameservers;
}

function odr_SaveNameservers($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    if (empty($result['response']['status']) || $result['response']['status'] !== 'REGISTERED') {
        return array(
            'error' => 'This domain nameserver details can not be changed, because only REGISTERED domains can be updated',
        );
    }

    $resp = $result['response'];

    $data = Odr_Whmcs::prepareDomainData($params, $resp['contacts_map']['REGISTRANT'], $resp['contacts_map']['ONSITE'], empty($resp['contacts_map']['TECH']) ? $resp['contacts_map']['ONSITE'] : $resp['contacts_map']['TECH']);

    try {
        $result = $module->updateDomain($params['domainname'], $data)->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    return array(
        'success' => true,
    );
}

function odr_GetContactDetails($params)
{
    $module   = odr_Config($params);
    $login    = odr_Login($module);
    $contacts = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $info = $module->getDomainInfo($params['sld'] . '.' . ltrim($params['tld'], '.'))->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($info['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($info['response']) ? $info['response']['message'] : $info['response']),
        );
    }

    $requestedContacts = array();

    foreach ($info['response']['contacts_map'] as $type => $cId) {
        if (!empty($requestedContacts[$cId])) {
            $contacts[Odr_Whmcs::mapContactTypeToWhmcs($type)] = $requestedContacts[$cId];

            continue;
        }

        try {
            $contact = $module->getContact($cId)->getResult();
        } catch (Exception $e) {
            return array(
                'error' => 'Following error occurred: ' . $e->getMessage(),
            );
        }

        if ($contact['status'] !== Api_Odr::STATUS_SUCCESS) {
            return array(
                'error' => 'Following error occurred: ' . (is_array($contact['response']) ? $contact['response']['message'] : $contact['response']),
            );
        }

        $contacts[Odr_Whmcs::mapContactTypeToWhmcs($type)] = Odr_Whmcs::odrContactDataToWhmcs($contact['response']);

        $requestedContacts[$cId] = $contacts[Odr_Whmcs::mapContactTypeToWhmcs($type)];
    }

    return $contacts;
}

function odr_SaveContactDetails($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    try {
        $info = $module->getDomainInfo($params['sld'] . '.' . ltrim($params['tld'], '.'))->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($info['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($info['response']) ? $info['response']['message'] : $info['response']),
        );
    }

    if (empty($info['response']['status']) || $info['response']['status'] !== 'REGISTERED') {
        return array(
            'error' => 'This domain contact details can not be changed, because only REGISTERED domains can be updated',
        );
    }

    if (empty($params['contactdetails'])) {
        return array(
            'error' => 'Specify at least some contact info before saving',
        );
    }

    $contacts = array();

    foreach ($params['contactdetails'] as $type => $contact) {
        $contactId = Odr_Whmcs::obtainContact($module, $contact);

        $contacts[Odr_Whmcs::mapContactTypeToOdr($type)] = $contactId;
    }

    foreach (array('REGISTRANT', 'ONSITE', 'TECH') as $type) {
        if (empty($contacts[$type])) {
            $contacts[$type] = $info['contacts_map'][$type];
        }
    }

    $data = Odr_Whmcs::prepareDomainData($params, $contacts['REGISTRANT'], $contacts['ONSITE'], empty($contacts['TECH']) ? $contacts['ONSITE'] : $contacts['TECH']);

    $i = 1;

    foreach ($info['response']['nameservers'] as $ns) {
        if (is_string($ns) && !$ns) {
            continue;
        }

        if (is_array($ns) && empty($ns['host'])) {
            continue;
        }

        $data["ns{$i}"] = $ns;

        $i++;
    }

    try {
        $result = $module->updateDomain($params['domainname'], $data)->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    return array(
        'success' => true,
    );
}

function odr_RegisterDomain($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

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
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    return array(
        'success' => $result['response']['status'] === 'COMPLETED',
    );
}

function odr_TransferDomain($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    $contactId = Odr_Whmcs::obtainContact($module, $params);

    if (!empty($contactId['error'])) {
        return $contactId;
    }

    $domainData = Odr_Whmcs::prepareDomainData($params, $contactId);

    try {
        $result = $module->transferDomain($params['domainname'], $domainData)->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    return array(
        'success' => $result['response']['status'] === 'COMPLETED',
    );
}

function odr_RenewDomain($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    if ($result['response']['status'] !== 'QUARANTINE') {
        $response = array();

        if ($result['response']['autorenew'] === 'OFF') {
            return array(
                'error' => 'Renewal is impossible',
            );
        }

        $response['status'] = 'Domain Renewed';

        return $response;
    }

    $reactivate = $module->reactivateDomain($params['domainname'])->getResult();

    if ($reactivate['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($reactivate['response']) ? $reactivate['response']['message'] : $reactivate['response']),
        );
    }

    if (!$reactivate['response']['result']) {
        return array(
            'error' => 'Reactivation is impossible',
        );
    }

    return array(
        'status' => 'Domain Reactivated',
    );
}

function odr_GetEPPCode($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainAuthCode($params['domainname'])->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    if (empty($result['response']['auth_code'])) {
        return array(
            'error' => 'Either EPP code not supported or it was sent to domain owner email address',
        );
    }

    if (is_string($result['response']['auth_code'])) {
        return array(
            'eppcode' => $result['response']['auth_code'],
        );
    }

    return array(
        'eppcode' => 'EPP code was sent to domain owner email address',
    );
}

function odr_TransferSync($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomain($params['domainname'])->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    $response = array();

    if ($result['response']['status'] === 'REGISTERED') {
        $response['completed']  = true;
        $response['expirydate'] = $result['response']['expiration_date'];
    } elseif ($result['response']['status'] === 'PENDING') {
        $response['error'] = 'Domain ' . $params['domainname'] . ' is still in the following status: ' . $result['response']['status'];
    } else {
        $response['failed'] = true;
        $response['reason'] = 'Domain ' . $params['domainname'] . ' is currently in the following status: ' . $result['response']['status'];
    }

    return $response;
}

function odr_Sync($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    if (empty($params['domainname']) && !empty($params['domain'])) {
        $params['domainname'] = $params['domain'];
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => '[ERROR] Sync contact | Error retrieving domain details ODR for ' . $params['domainname'],
        );
    }

    $response = array();

    $domain = $result['response'];

    // Sync domain status
    if ($domain['status'] === 'REGISTERED') {
        $response['active'] = true;
    } elseif ($domain['status'] === 'DELETED' || $domain['status'] === 'QUARANTINE') {
        $response['expired'] = true;
    }

    $response['expirydate'] = $domain['expiration_date'];

    return $response;
}

function odr_CheckAvailability($params)
{
    return odr_GetDomainSuggestions($params);
}

function odr_GetDomainSuggestions($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    $domains = array();

    $name = empty($params['sld']) ? $params['searchTerm'] : $params['sld'];

    if (empty($params['tld'])) {
        foreach ($params['tldsToInclude'] as $tld) {
            $tld = ltrim($tld, '.');

            $domains[] = array(
                'full' => $name . '.' . $tld,
                'sld'  => $name,
                'tld'  => $tld,
            );
        }
    } else {
        $tld = ltrim($params['tld'], '.');

        $domains[] = array(
            'full' => $name . '.' . $tld,
            'sld'  => $name,
            'tld'  => $tld,
        );
    }

    return Odr_Whmcs::checkDomainsState($module, $domains);
}

function odr_RequestDelete($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->setDomainAutorenewOff($params['domainname'])->getResult();
    } catch (\Exception $e) {
        return array(
            'error' => 'Following error occurred: ' . $e->getMessage(),
        );
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        return array(
            'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
        );
    }

    if (!$result['response']['result']) {
        return array(
            'error' => 'Domain deletion already scheduled',
        );
    }

    $response = array();

    $response['domainid'] = $params['domainid'];
    $response['status']   = 'Cancelled';

    return $response;
}

class Odr_Whmcs
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';

    const URL_TEST = 'http://apiodr.kaa.tomoko.rosapp.ru';
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

    /**
     * Performs a search over all available contacts
     *
     * @param string $name     Contact name
     * @param array  $contacts All available contacts
     *
     * @return null|int
     */
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

    /**
     * Correctly formats contact name
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $companyName
     *
     * @return string
     */
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

    /**
     * Returns first letter of name, uppercased
     *
     * @param string $name
     *
     * @return string
     */
    static public function getInitials($name)
    {
        $initials = mb_substr($name, 0, 1);

        return mb_strtoupper($initials);
    }

    /**
     * Returns uppercased initials of each name piece
     * For example:
     * Alex Name = AN
     * Name Alex Fed = NAF
     * john test = JT
     *
     * @param string $name
     *
     * @return string
     */
    static public function getInitialsFull($name)
    {
        $initials = '';
        $exploded = explode(' ', $name);

        foreach ($exploded as $letter) {
            $initials .= mb_substr($letter, 0, 1);
        }

        return mb_strtoupper($initials);
    }

    /**
     * Returns array of available countries
     *
     * @return array
     */
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

    /**
     * Returns country data
     * If country is unknown, 'nl' will be used
     *
     * @param string $country
     *
     * @return array
     */
    static public function getCountry($country)
    {
        $countries = self::getCountries();

        $country = strtolower($country);

        if (!array_key_exists($country, $countries)) {
            $country = 'nl';
        }

        return $countries[$country];
    }

    /**
     * Formats a phone
     *
     * @param string $country
     * @param string $phone
     *
     * @return string
     */
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
        $fax         = empty($whmcs['faxnumber']) ? null : self::getPhone($whmcs['faxnumber']);
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
            'fax'          => $fax === null ? null : self::formatPhone($country['phone'], $fax),
            'postal_code'  => $zip,

            'organization_legal_form' => $legalForm,

            'company_name'         => $companyName,
            'company_city'         => $whmcs['city'],
            'company_email'        => $whmcs['email'],
            'company_phone'        => self::formatPhone($country['phone'], $phone),
            'company_fax'          => $fax === null ? null : self::formatPhone($country['phone'], $fax),
            'company_postal_code'  => $zip,
            'company_street'       => $street['street'],
            'company_house_number' => $houseNumber,
        );
    }

    /**
     * Converts ODR data to WHMCS format
     *
     * @param array $odr Source data
     *
     * @return array|string|null Formatted data
     *
     * @static
     */
    static public function odrContactDataToWhmcs(array $odr)
    {
        return array(
            'firstname'   => $odr['first_name'],
            'lastname'    => $odr['last_name'],
            'companyname' => $odr['organization_legal_form'] === Odr_Whmcs::LEGAL_FORM_PERSON ? '' : $odr['company_name'],
            'email'       => $odr['email'],
            'address1'    => $odr['street'] . ', ' . $odr['house_number'],
            'address2'    => '',
            'city'        => $odr['city'],
            'state'       => $odr['state'],
            'postcode'    => $odr['postal_code'],
            'country'     => $odr['country'],
            'phonenumber' => $odr['phone'],
            'faxnumber'   => $odr['fax'],
        );
    }

    /**
     * Returns ODR API module instance, based on passed parameters
     *
     * @param array $params
     *
     * @return Api_Odr|null
     */
    static public function getModule(array $params)
    {
        if (self::$module) {
            return self::$module;
        }

        $isTestmode = false;

        if (!empty($params['OdrTestmode'])) {
            $isTestmode = true;
        }

        $module = array(
            'api_key'     => $isTestmode && $params['OdrTestApiKey']    ? $params['OdrTestApiKey']    : $params['OdrApiKey'],
            'api_secret'  => $isTestmode && $params['OdrTestApiSecret'] ? $params['OdrTestApiSecret'] : $params['OdrApiSecret'],
            'url'         => $isTestmode ? Odr_Whmcs::URL_TEST : Odr_Whmcs::URL_LIVE,
            'enable_logs' => !empty($params['OdrEnableLogs']),
            'logs_path'   => empty($params['OdrLogsPath']) ? __DIR__ . '/log/api#YEAR##MONTH##DAY#_#HOUR#.log' : $params['OdrLogsPath'],
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

    /**
     * Returns key, under which login state is stored in session array
     *
     * @return string
     */
    static public function getSessionKey()
    {
        return self::MODULE . '_loginState';
    }

    /**
     * Clears the data, then sorts result array by key
     *
     * @param array $contact
     *
     * @return array
     */
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

    /**
     * Returns array of useless array keys
     * This useless contact fields later will be removed, in 'reformatContact'
     *
     * @return array
     *
     * @see reformatContact
     */
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

    /**
     * Obtains contact
     * If contact is not found in ODR, new one will be created
     *
     * @param \Api_Odr $module
     * @param array    $params
     *
     * @return array|int|null
     */
    static public function obtainContact(Api_Odr $module, array $params)
    {
        $filters = array(
            'contact_name' => array(
                'name'         => $params['firstname'] . ' ' . $params['lastname'],
                'company_name' => $params['companyname'],
            ),
        );

        try {
            $result = $module->getContacts($filters)->getResult();
        } catch (\Exception $e) {
            return array(
                'error' => 'Following error occurred: ' . $e->getMessage(),
            );
        }

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return array(
                'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
            );
        }

        $contactId = Odr_Whmcs::searchContact(Odr_Whmcs::formatContactName($params['firstname'], $params['lastname'], $params['companyname']), $result['response']);

        // Contact doesn't exist, create a new
        if ($contactId === null) {
            $contactData = Odr_Whmcs::contactDataToOdr($params);

            if ($contactData === null || is_string($contactData)) {
                return array(
                    'error' => 'Following error occurred: ' . (is_string($contactData) ? $contactData : 'Contact creation is impossible, due to corrupted data'),
                );
            }

            $contactData = array_map('trim', $contactData);

            try {
                $result = $module->createContact($contactData)->getResult();
            } catch (\Exception $e) {
                return array(
                    'error' => 'Following error occurred: ' . $e->getMessage(),
                );
            }

            if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
                return array(
                    'error' => 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']),
                );
            }

            $contactId = empty($result['response']['data']['id']) ? null : $result['response']['data']['id'];
        }

        if ($contactId === null) {
            return array(
                'error' => 'Contact creation was not successful, please either try using different data or contact support',
            );
        }

        return $contactId;
    }

    /**
     * Prepares data for domain operations
     *
     * @param array    $params
     * @param int      $contactRegistrant
     * @param null|int $contactOnsite
     * @param null|int $contactTech
     *
     * @return array
     */
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

    /**
     * Checks the domain availability state
     *
     * @param \Api_Odr $module
     * @param array    $domains
     *
     * @return array|ResultsList
     */
    static public function checkDomainsState(Api_Odr $module, array $domains)
    {
        $results = new ResultsList();

        if (empty($domains)) {
            return $results;
        }

        foreach ($domains as $domain) {
            try {
                $result = $module->checkDomain($domain['full'])->getResult();
            } catch (\Exception $e) {
                return array(
                    'error' => 'Following error occurred: ' . $e->getMessage(),
                );
            }

            $searchResult = new SearchResult($domain['sld'], $domain['tld']);

            $status = SearchResult::STATUS_REGISTERED;

            if ($result['status'] !== Api_Odr::STATUS_ERROR && !empty($result['response']['is_available'])) {
                $status = SearchResult::STATUS_NOT_REGISTERED;
            }

            $searchResult->setStatus($status);

            $results->append($searchResult);
        }

        return $results;
    }

    /**
     * Maps ODR contact type to WHMCS contact type
     *
     * @param string $type
     *
     * @return null|string
     */
    static public function mapContactTypeToWhmcs($type)
    {
        $map = array_flip(self::getContactTypesMap());

        return empty($map[$type]) ? null : $map[$type];
    }

    /**
     * Maps WHMCS contact type to ODR contact type
     *
     * @param string $type
     *
     * @return null|string
     */
    static public function mapContactTypeToOdr($type)
    {
        $map = self::getContactTypesMap();

        return empty($map[$type]) ? null : $map[$type];
    }

    /**
     * Returns array of available contact types
     *
     * @return array
     */
    static public function getContactTypesMap()
    {
        return array(
            'Registrant' => 'REGISTRANT',
            'Technical'  => 'TECH',
            'Billing'    => 'BILLING',
            'Admin'      => 'ONSITE',
        );
    }
}