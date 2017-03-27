<?php
namespace WHMCS\Domains\DomainLookup;

class ResultsList
{
    protected $_results = array();

    public function append(SearchResult $result)
    {
        $this->_results[$result->domain . '.' . $result->tld] = $result->status;
    }

    public function getResults()
    {
        return $this->_results;
    }
}