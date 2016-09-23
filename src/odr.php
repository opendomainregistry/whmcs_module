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

        'Adminuser'      => array(
            'FriendlyName' => 'Admin user',
            'Type'         => 'text',
            'Default'      => 'admin',
            'Size'         => '50',
        ),

        'Synccontact'    => array(
            'FriendlyName' => 'Sync Contact',
            'Type'         => 'yesno',
        ),

        'Syncdomain'     => array(
            'FriendlyName' => 'Sync Domain',
            'Type'         => 'yesno',
        ),

        'Testmode'       => array(
            'FriendlyName' => 'Test Mode',
            'Type'         => 'yesno',
            'Default'      => false,
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

    // Check if domain is active
    $values['domainid'] = $params['domainid'];

    //$result = localAPI('getclientsdomains', $values, $params['Adminuser']);

    //logModuleCall(Odr_Whmcs::MODULE, 'GetNameservers', $params, $result, '', '');

    if (empty($result['domains']['domain'][0]['status']) || $result['domains']['domain'][0]['status'] !== 'Active') {
        //return $values;
    }

    try {
        $result = $module->getDomainInfo($params['domainname'])->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    logModuleCall(Odr_Whmcs::MODULE, 'GetNameservers | Retrieve domain details', $params['domainname'], $result, '', '');

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
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    logModuleCall('ODR', 'SaveNameservers | Get orginal value', $params['domainname'], $result, '', '');

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    $resp = $result['response'];

    $data['period']             = $params['regperiod'];
    $data['contact_registrant'] = $resp['contact_registrant'];
    $data['contact_onsite']     = $resp['contact_onsite'];
    $data['contact_tech']       = $resp['contact_tech'];
    $data['ns1']                = $params['ns1'];
    $data['ns2']                = $params['ns2'];
    $data['ns3']                = $params['ns3'];
    $data['ns4']                = $params['ns4'];
    $data['ns5']                = $params['ns5'];

    try {
        $result = $module->custom('/domain/' . $params['domainname'] . '/', Api_Odr::METHOD_PUT, $data)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    logModuleCall('ODR', 'SaveNameservers | Save new value', $data, $result, '', '');

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

    try {
        $result = $module->custom('/contact/', Api_Odr::METHOD_GET)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    $contactId = Odr_Whmcs::searchContact(Odr_Whmcs::formatContactName($params['firstname'], $params['lastname'], $params['companyname']), $result['response']);

    logModuleCall('ODR', 'RegisterDomain | List contacts', $result, $contactId, '', '');

    // Contact doesn't exist, create a new
    if ($contactId === null) {
        $contactData = Odr_Whmcs::contactDataToOdr($params);

        if ($contactData === null || is_string($contactData)) {
            $values['error'] = 'Following error occurred: ' . (is_string($contactData) ? $contactData : 'Contact creation is impossible, due to corrupted data');

            return $values;
        }

        try {
            $result = $module->custom('/contact/', Api_Odr::METHOD_POST, $contactData)->getResult();
        } catch (\Exception $e) {
            $values['error'] = 'Following error occurred: ' . $e->getMessage();

            return $values;
        }
        die(var_dump('RESULT', $result, $contactData));

        logModuleCall('ODR', 'RegisterDomain | Create contact', $contactData, $result, '', '');

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

            return $values;
        }

        $contactId = $result['response']['contact_id'];
    }
    die(var_dump('Params', $contactId, $params));

    if ($contactId === null) {
        $values['error'] = 'Contact creation was not successful, please either try using different data or contact support';

        return $values;
    }

    $domainData['period']             = $params['regperiod'];
    $domainData['contact_registrant'] = $contactId;
    $domainData['contact_onsite']     = $contactId;
    $domainData['contact_tech']       = $contactId;
    $domainData['ns1']['host']        = $params['ns1'];
    $domainData['ns2']['host']        = $params['ns2'];

    try {
        $result = $module->registerDomain($params['domainname'], $domainData)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    logModuleCall('ODR', 'RegisterDomain | Transfer domain', $domainData, $result, '', '');

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

    try {
        $result = $module->custom('/contact/', Api_Odr::METHOD_GET)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    $contactId = Odr_Whmcs::searchContact(Odr_Whmcs::formatContactName($params['firstname'], $params['lastname'], $params['companyname']), $result['response']);

    logModuleCall('ODR', 'TransferDomain | List contacts', $result, $contactId, '', '');

    if (!$contactId) {
        $contactData = Odr_Whmcs::contactDataToOdr($params);

        try {
            $result = $module->custom('/contact/', Api_Odr::METHOD_POST, $contactData)->getResult();
        } catch (\Exception $e) {
            $values['error'] = 'Following error occurred: ' . $e->getMessage();

            return $values;
        }

        logModuleCall('ODR', 'TransferDomain | Create contact', $contactData, $result, '', '');

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

            return $values;
        }

        $contactId = $result['response']['contact_id'];
    }

    if ($contactId === null) {
        $values['error'] = 'Contact creation was not successful, please either try using different data or contact support';

        return $values;
    }

    $domainData['auth_code']          = $params['transfersecret'];
    $domainData['period']             = $params['regperiod'];
    $domainData['contact_registrant'] = $contactId;
    $domainData['contact_onsite']     = $contactId;
    $domainData['contact_tech']       = $contactId;
    $domainData['ns1']['host']        = $params['ns1'];
    $domainData['ns2']['host']        = $params['ns2'];

    try {
        $result = $module->transferDomain($params['domainname'], $domainData)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    logModuleCall('ODR', 'TransferDomain | Transfer domain', $domainData, $result, '', '');

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    return $values;
}

function odr_RenewDomain($params)
{
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $regperiod = $params['regperiod'];
    # Put your code to renew domain here
    # If error, return the error message in the value below
    $values['error'] = $error;

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
        $result = $module->custom('/domain/' . $params['domainname'] . '/', Api_Odr::METHOD_GET)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    logModuleCall('ODR', 'TransferSync | Retrieve domain status', $params['domainname'], $result, '', '');

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

/**
 * This function retrieves the contact details of the domain by WHMCS and ODR and compares them
 * If there are differences a new contact is created
 *
 * @param Api_Odr $module
 * @param array   $params
 * @param int     $contactWhmcsId
 * @param int     $contactOdrId
 *
 * @return array
 */
function odr_Sync_contact($module, $params, $contactWhmcsId, $contactOdrId)
{
    $values['clientid'] = $contactWhmcsId;

    $result = localAPI('getclientsdetails', $values, $params['Adminuser']);

    if ($result['result'] !== Odr_Whmcs::STATUS_SUCCESS) {
        logModuleCall('ODR Sync contact', 'Retrieve contact details WHMCS |' . $params['domain'], $values['clientid'], $result, '', '');

        return array(
            'status' => Odr_Whmcs::STATUS_ERROR,
            'error'  => 'Error occured while retrieving contact details in WHMCS for ' . $params['domain'],
        );
    }

    $contactWhmcs = Odr_Whmcs::contactDataToOdr($result);
    $contactOdr   = array();

    if ($contactOdrId !== null) {
        try {
            $result = $module->custom('/contact/' . $contactOdrId . '/', Api_Odr::METHOD_GET)->getResult();
        } catch (\Exception $e) {
            $values['error'] = 'Following error occurred: ' . $e->getMessage();

            return $values;
        }

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            logModuleCall('ODR Sync contact', 'Retrieve contact details ODR |' . $params['domain'], $contactOdrId, $result, '', '');

            return array(
                'status' => Odr_Whmcs::STATUS_ERROR,
                'error'  => 'Error occurred while retrieving ODR contact for ' . $params['domain'],
            );
        }

        $contactOdr = odr_format_contact_odr($result['response']['contact']);
    }

    if (empty($contactOdr) || $contactOdr['full_name'] !== $contactWhmcs['full_name'] || $contactOdr['company_name'] !== $contactWhmcs['company_name'] || $contactOdr['organization_legal_form'] !== $contactWhmcs['organization_legal_form'] || !$contactOdrId > 1000) {
        logModuleCall('ODR Sync contact', 'Check contact |' . $params['domain'], $contactWhmcs, $contactOdr, '', '');

        try {
            $result = $module->custom('/contact/', Api_Odr::METHOD_POST, $contactWhmcs)->getResult();
        } catch (\Exception $e) {
            $values['error'] = 'Following error occurred: ' . $e->getMessage();

            return $values;
        }

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            logModuleCall('ODR Sync contact', 'Create ODR contact |' . $params['domain'], $contactWhmcs, $result, '', '');

            return array(
                'status' => Odr_Whmcs::STATUS_ERROR,
                'error'  => 'Error occured while creating ODR contact for  ' . $params['domain'],
            );
        }

        $values['description'] = '[INFO] Sync contact | Created contact for ' . $params['domain'];

        localAPI('logactivity', $values, $params['Adminuser']);

        return array(
            'status' => Odr_Whmcs::STATUS_SUCCESS,
            'handle' => $result['response']['contact_id'],
        );
    }

    $result = array_diff($contactWhmcs, $contactOdr);

    if (count($result) > 0) {
        logModuleCall('ODR Sync contact', 'Compare WHMCS ODR contact |' . $params['domain'], $contactWhmcs, $contactOdr, '', '');

        try {
            $result = $module->custom('/contact/' . $contactOdrId . '/', Api_Odr::METHOD_PUT, $contactWhmcs)->getResult();
        } catch (\Exception $e) {
            $values['error'] = 'Following error occurred: ' . $e->getMessage();

            return $values;
        }

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            logModuleCall('ODR Sync contact', 'Update ODR contact |' . $params['domain'], $contactOdrId, $contactWhmcs, '', '');

            return array(
                'status' => Odr_Whmcs::STATUS_ERROR,
                'error'  => 'Error occurred while updating contact for ' . $params['domain'],
            );
        }

        $values['description'] = '[INFO] Sync contact | Updated contact for ' . $params['domain'];

        localAPI('logactivity', $values, $params['Adminuser']);
    }

    return array(
        'status' => Odr_Whmcs::STATUS_SUCCESS,
        'handle' => $contactOdrId,
    );
}

/**
 * This function will check if there are any differences in the ODR handles
 * If there are any, the domain will be updated
 *
 * @param Api_Odr $module
 * @param array   $params
 * @param array   $domainOdr
 * @param int     $handle
 *
 * @return array
 */
function odr_Sync_handle($module, $params, $domainOdr, $handle)
{
    if (!$handle && !empty($domainOdr['contact_tech'])) {
        $handle = $domainOdr['contact_tech'];
    }

    if (!$handle && !empty($domainOdr['contact_onsite'])) {
        $handle = $domainOdr['contact_onsite'];
    }

    if (!$handle && !empty($domainOdr['contact_registrant'])) {
        $handle = $domainOdr['contact_registrant'];
    }

    if (!$handle) {
        return array(
            'status' => Odr_Whmcs::STATUS_SUCCESS,
        );
    }

    $domainWhmcs['period'] = $domainOdr['period'];

    $domainWhmcs['contact_registrant'] = $domainOdr['contact_registrant'];
    $domainWhmcs['contact_onsite']     = $handle;
    $domainWhmcs['contact_tech']       = $handle;

    foreach (range(1, 8) as $r) {
        $k = 'ns' . $r;

        $domainWhmcs[$k] = empty($domainOdr[$k]) ? null : $domainOdr[$k];
    }

    try {
        $result = $module->custom('/domain/' . $params['domain'] . '/', Api_Odr::METHOD_PUT, $domainWhmcs)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        logModuleCall('ODR Sync handle', '| Update domain details ODR |' . $params['domain'], $domainWhmcs, $result, '', '');

        return array(
            'status' => Odr_Whmcs::STATUS_ERROR,
            'error'  => 'Error occurred while updating ODR domain: ' . $params['domain'],
        );
    }

    $values['description'] = 'Sync handle ' . $params['domain'] . '| Changed handle for domain ' . $params['domain'];

    localAPI('logactivity', $values, $params['Adminuser']);

    return array(
        'status' => Odr_Whmcs::STATUS_SUCCESS,
    );
}

function odr_Sync($params)
{
    $module = odr_Config($params);
    $login  = odr_Login($module);
    $values = array();

    if ($login !== true) {
        return $login;
    }

    try {
        $result = $module->getDomainInfo($params['domain'])->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['description'] = '[ERROR] Sync contact | Error retrieving domain details ODR for ' . $params['domain'];

        localAPI('logactivity', $values, $params['Adminuser']);

        logModuleCall('ODR Sync', 'Retrieve domain details ODR |' . $params['domain'], $params['domain'], $result, '', '');

        $values['error'] = $values['description'];

        return $values;
    }

    $domainOdr = $result['response'];

    //Sync domain status
    if ($domainOdr['status'] === 'REGISTERED') {
        $values['active'] = true;
    } elseif ($domainOdr['status'] === 'DELETED') {
        $values['expired'] = true;
    }

    $values['expirydate'] = $domainOdr['expiration_date'];

    if ($params['Synccontact']) {
        $result = localAPI('getclientsdomains', $params, $params['Adminuser']);

        if ($result['result'] !== Odr_Whmcs::STATUS_SUCCESS) {
            $values['description'] = '[ERROR] Sync contact | Error retrieving domain details WHMCS for ' . $params['domain'];

            localAPI('logactivity', $values, $params['Adminuser']);
            logModuleCall('ODR Sync', 'Retrieve domain details WHMCS |' . $params['domain'], $params['domain'], $result, '', '');

            $values['error'] = $values['description'];

            return $values;
        }

        $domainWhmcs = $result['domains']['domain'][0];

        $result = odr_Sync_contact($module, $params, $domainWhmcs['userid'], $domainOdr['contact_registrant']);

        if ($result['status'] !== Odr_Whmcs::STATUS_SUCCESS) {
            $values['description'] = '[ERROR] Sync contact |' . $result['error'];
            $values['error']       = $result['error'];

            localAPI('logactivity', $values, $params['Adminuser']);

            return $values;
        }

        $handle = $result['handle'];

        $result = odr_Sync_handle($module, $params, $domainOdr, $handle);

        if ($result['status'] !== Odr_Whmcs::STATUS_SUCCESS) {
            $values['description'] = '[ERROR] Sync handle |' . $result['error'];
            $values['error']       = $result['error'];

            localAPI('logactivity', $values, $params['Adminuser']);

            return $values;
        }
    }

    if ($params['Syncdomain']) {
        $result = Odr_Whmcs::syncDomain($module, $params);

        if ($result['status'] !== Odr_Whmcs::STATUS_SUCCESS) {
            $values['description'] = '[ERROR] Sync domain |' . $result['error'];
            $values['error']       = $result['error'];

            localAPI('logactivity', $values, $params['Adminuser']);

            return $values;
        }
    }

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
        $result = $module->custom('/domain/' . $params['domainname'] . '/renew-off/', Api_Odr::METHOD_PUT)->getResult();
    } catch (\Exception $e) {
        $values['error'] = 'Following error occurred: ' . $e->getMessage();

        return $values;
    }

    logModuleCall('ODR', 'RequestDelete | Cancel domain in ODR', $params['domainname'], $result, '', '');

    if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
        $values['error'] = 'Following error occurred: ' . (is_array($result['response']) ? $result['response']['message'] : $result['response']);

        return $values;
    }

    $values['domainid'] = $params['domainid'];
    $values['status']   = 'Cancelled';

    $result = localAPI('updateclientdomain', $values, $params['Adminuser']);

    logModuleCall('ODR', 'RequestDelete | Cancel domain in WHMCS', $values, $result, '', '');

    return $values;
}

function odr_format_contact_odr($data)
{
    unset($data['id']);
    unset($data['customer_id']);
    unset($data['comment']);
    unset($data['created']);
    unset($data['updated']);
    unset($data['is_filled']);
    unset($data['birthday']);
    unset($data['url']);
    unset($data['company_url']);
    unset($data['company_vatin']);
    unset($data['company_kvk']);
    unset($data['company_address']);
    ksort($data);

    return $data;
}

/* function odr_RegisterNameserver($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $nameserver = $params['nameserver'];
    $ipaddress = $params['ipaddress'];
    # Put your code to register the nameserver here
    # If error, return the error message in the value below
    $values['error'] = $error;
    return $values;
}
 */
/* function odr_ModifyNameserver($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $nameserver = $params['nameserver'];
    $currentipaddress = $params['currentipaddress'];
    $newipaddress = $params['newipaddress'];
    # Put your code to update the nameserver here
    # If error, return the error message in the value below
    $values['error'] = $error;
    return $values;
}
 */
/* function odr_DeleteNameserver($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    $nameserver = $params['nameserver'];
    # Put your code to delete the nameserver here
    # If error, return the error message in the value below
    $values['error'] = $error;
    return $values;
} */

/* function odr_GetRegistrarLock($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    # Put your code to get the lock status here
    if ($lock=='1') {
        $lockstatus='locked';
    } else {
        $lockstatus='unlocked';
    }
    return $lockstatus;
} */

/* function odr_SaveRegistrarLock($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    if ($params['lockenabled']) {
        $lockstatus='locked';
    } else {
        $lockstatus='unlocked';
    }
    # Put your code to save the registrar lock here
    # If error, return the error message in the value below
    $values['error'] = $Enom->Values['Err1'];
    return $values;
} */

/* function odr_GetEmailForwarding($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    # Put your code to get email forwarding here - the result should be an array of prefixes and forward to emails (max 10)
    foreach ($result AS $value) {
        $values[$counter]['prefix'] = $value['prefix'];
        $values[$counter]['forwardto'] = $value['forwardto'];
    }
    return $values;
} */

/* function odr_SaveEmailForwarding($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    foreach ($params['prefix'] AS $key=>$value) {
        $forwardarray[$key]['prefix'] =  $params['prefix'][$key];
        $forwardarray[$key]['forwardto'] =  $params['forwardto'][$key];
    }
    # Put your code to save email forwarders here
} */

/* function odr_GetDNS($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    # Put your code here to get the current DNS settings - the result should be an array of hostname, record type, and address
    $hostrecords = array();
    $hostrecords[] = array( 'hostname' => 'ns1', 'type' => 'A', 'address' => '192.168.0.1', );
    $hostrecords[] = array( 'hostname' => 'ns2', 'type' => 'A', 'address' => '192.168.0.2', );
    return $hostrecords;
} */

/* function odr_SaveDNS($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    # Loop through the submitted records
    foreach ($params['dnsrecords'] AS $key=>$values) {
        $hostname = $values['hostname'];
        $type = $values['type'];
        $address = $values['address'];
        # Add your code to update the record here
    }
    # If error, return the error message in the value below
    $values['error'] = $Enom->Values['Err1'];
    return $values;
} */

/* function odr_GetContactDetails($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    # Put your code to get WHOIS data here
    # Data should be returned in an array as follows
    $values['Registrant']['First Name'] = $firstname;
    $values['Registrant']['Last Name'] = $lastname;
    $values['Admin']['First Name'] = $adminfirstname;
    $values['Admin']['Last Name'] = $adminlastname;
    $values['Tech']['First Name'] = $techfirstname;
    $values['Tech']['Last Name'] = $techlastname;
    return $values;
} */

/* function odr_SaveContactDetails($params) {
    $username = $params['Username'];
    $password = $params['Password'];
    $testmode = $params['TestMode'];
    $tld = $params['tld'];
    $sld = $params['sld'];
    # Data is returned as specified in the GetContactDetails() function
    $firstname = $params['contactdetails']['Registrant']['First Name'];
    $lastname = $params['contactdetails']['Registrant']['Last Name'];
    $adminfirstname = $params['contactdetails']['Admin']['First Name'];
    $adminlastname = $params['contactdetails']['Admin']['Last Name'];
    $techfirstname = $params['contactdetails']['Tech']['First Name'];
    $techlastname = $params['contactdetails']['Tech']['Last Name'];
    # Put your code to save new WHOIS data here
    # If error, return the error message in the value below
    $values['error'] = $error;
    return $values;
} */

class Odr_Whmcs
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';

    /*const URL_TEST = 'http://api.odrregistry.nl';*/
    const URL_TEST = 'http://apiodr.kaa.tomiko.rosapp.ru';
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
     * @param Api_Odr $module
     * @param array   $params
     *
     * @return array
     *
     * @static
     */
    static public function syncDomain(Api_Odr $module, array $params)
    {
        $send_email = false;

        $values['limitnum']      = 9999;
        $values['custommessage'] = '<h3>ODR Domain Synchronization Report</h3>';

        $domainsWhmcs = localAPI('getclientsdomains', $values, $params['Adminuser']);

        if ($domainsWhmcs['result'] !== self::STATUS_SUCCESS) {
            logModuleCall('ODR Sync contact', 'Retrieve domains WHMCS |' . '', $values, $domainsWhmcs, '', '');

            return array(
                'status' => self::STATUS_ERROR,
                'error'  => 'Error occured while retrieving WHMCS domains',
            );
        }

        try {
            $result = $module->custom('/domain/', Api_Odr::METHOD_GET)->getResult();
        } catch (\Exception $e) {
            $values['error'] = 'Following error occurred: ' . $e->getMessage();

            return $values;
        }

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            logModuleCall('ODR Sync domain', '| Retrieve domains ODR |' . '', '', $result, '', '');

            return array(
                'status' => self::STATUS_ERROR,
                'error'  => 'Error occurred with retrieving ODR domains',
            );
        }

        $domainsOdr = array();

        foreach ($result['response'] as $i => $domain) {
            $domainsOdr[$domain['domain_name']] = $domain['domain_name'];
        }

        $values['custommessage'] .= '<h3>Active domains not in ODR</h3>';
        $values['custommessage'] .= '<ul>';

        foreach ($domainsWhmcs['domains']['domain'] as $i => $domain) {
            if ($domain['registrar'] !== Odr_Whmcs::MODULE) {
                continue;
            }

            if (!in_array($domain['status'], array('Active', 'Expired'), true)) {
                continue;
            }

            if (isset($domainsOdr[$domain['domainname']])) {
                unset($domainsOdr[$domain['domainname']]);

                continue;
            }

            $values['custommessage'] .= '<li>' . $domain['domainname'] . '</li>';

            $send_email = true;
        }

        $values['custommessage'] .= '</ul>';

        $values['custommessage'] .= '<h3>Active domains not in WHMCS</h3>';
        $values['custommessage'] .= '<ul>';

        foreach ($domainsOdr as $domain) {
            try {
                $result = $module->custom('/domain/' . $domain . '/', Api_Odr::METHOD_GET)->getResult();
            } catch (\Exception $e) {
                $values['error'] = 'Following error occurred: ' . $e->getMessage();

                return $values;
            }

            if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
                logModuleCall('ODR Sync domain', '| Filter active domains |' . $domain, $domain, $result, '', '');

                $values['custommessage'] .= '<li>Error checking ' . $result['response']['domain_name'] . '.' . $result['response']['tld'] . '</li>';
            } else if ($result['response']['domain_status'] == 'REGISTERED') {
                $values['custommessage'] .= '<li>' . $result['response']['domain_name'] . '.' . $result['response']['tld'] . '</li>';

                $send_email = true;
            }
        }

        if ($send_email) {
            $values['customtype']    = 'domain';
            $values['customsubject'] = 'WHMCS ODR Sync Job Activity';

            $values['id'] = $params['domainid'];

            $result = localAPI('sendemail', $values, $params['Adminuser']);

            if ($result['result'] !== self::STATUS_SUCCESS) {
                logModuleCall('ODR Sync domains', 'Send email |' . '', $params, $result, '', '');

                return array(
                    'status' => self::STATUS_ERROR,
                    'error'  => 'Error occured while sending the email report',
                );
            }
        }

        return array(
            'status' => self::STATUS_SUCCESS,
        );
    }

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
            'api_key'    => $isTestmode && $params['TestApiKey']    ? $params['TestApiKey']    : $params['ApiKey'],
            'api_secret' => $isTestmode && $params['TestApiSecret'] ? $params['TestApiSecret'] : $params['ApiSecret'],
            'url'        => $isTestmode ? Odr_Whmcs::URL_TEST : Odr_Whmcs::URL_LIVE,
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
}