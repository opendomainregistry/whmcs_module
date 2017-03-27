<?php
if (!function_exists('localAPI')) {
    /**
     * @param $action
     * @param $values
     * @param $user
     *
     * @return mixed
     */
    function localAPI($action, $values, $user)
    {
    }
}

if (!function_exists('logModuleCall')) {
    /**
     * @param $module
     * @param $action
     * @param $params
     * @param $result
     * @param $a
     * @param $b
     *
     * @return mixed
     */
    function logModuleCall($module, $action, $params, $result, $a = '', $b = '')
    {
    }
}

if (!class_exists('WHMCS\Domains\DomainLookup\ResultsList')) {
    require_once __DIR__ . '/f_resultslist.php';
}

if (!class_exists('WHMCS\Domains\DomainLookup\SearchResult')) {
    require_once __DIR__ . '/f_searchresult.php';
}