<?php

namespace ADAL;

class RequestData
{
    public Authenticator $authenticator;

    public $tokenCache;

    public string $resource;

    public ClientKey $clientKey;

    public $subjectType;

    public bool $extendedLifeTimeEnabled;

    public function __construct(Authenticator $athenticator, $tokenCache, $resource, $clientKey, $extendedLifeTimeEnabled)
    {
        $this->authenticator = $this->authenticator;
        $this->tokenCache = $this->tokenCache;
        $this->resource = $resource;
        $this->clientKey = new ClientKey($clientId);
        $this->extendedLifeTimeEnabled = $this->extendedLifeTimeEnabled;
    }
}
