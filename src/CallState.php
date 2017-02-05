<?php

namespace ADAL;

class CallState
{
    private $correlationId;

    private $authorityType;

    public function __construct(string $correlationId)
    {
        $this->correlationId = $correlationId;
    }

    public function getCorrelationId()
    {
        return $this->correlationId;
    }

    public function setCorrelationId($correlationId)
    {
        return $this->correlationId = $correlationId;
    }

    public function getAuthorityType()
    {
        return $this->authorityType;
    }
}
