<?php
namespace WHMCS\Domains\DomainLookup;

class SearchResult
{
    const STATUS_NOT_REGISTERED    = 'NOT_REGISTERED';
    const STATUS_REGISTERED        = 'REGISTERED';
    const STATUS_RESERVED          = 'RESERVED';
    const STATUS_TLD_NOT_SUPPORTED = 'TLD_NOT_SUPPORTED';

    public $domain;
    public $tld;
    public $status;

    public function __construct($domain, $tld)
    {
        $this->domain = $domain;
        $this->tld    = $tld;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}