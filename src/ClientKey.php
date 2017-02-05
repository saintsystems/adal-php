<?php

namespace ADAL;

class ClientKey
{
    public function __construct(string $clientId)
    {
        if (empty($clientId))
        {
            throw new ArgumentNullException('$clientId');
        }

        $this->clientId = $clientId;
        $this->hasCredential = false;
    }

    public static function fromClientCredential(ClientCredential $clientCredential)
    {
        $clientKey = new self;
        if ($clientCredential == null)
        {
            throw new ArgumentNullException('$clientCredential');
        }

        $clientKey->credential = $clientCredential;
        $clientKey->clientId = $clientCredential->clientId;
        $clientKey->hasCredential = true;
        return $clientKey;
    }

    public static function fromClientCertificate(IClientAssertionCertificate $clientCertificate, Authenticator $authenticator)
    {
        $clientKey = new self;
        $clientKey->authenticator = $authenticator;

        if ($clientCertificate == null)
        {
            throw new ArgumentNullException('$clientCertificate');
        }

        $clientKey->certificate = $clientCertificate;
        $clientKey->clientId = $clientCertificate->clientId;
        $clientKey->hasCredential = true;
        return $clientKey;
    }

    // public ClientKey(ClientAssertion clientAssertion)
    // {
    //     if (clientAssertion == null)
    //     {
    //         throw new ArgumentNullException("clientAssertion");
    //     }

    //     this.Assertion = clientAssertion;
    //     this.ClientId = clientAssertion.ClientId;
    //     this.HasCredential = true;
    // }

    public ClientCredential $credential

    public IClientAssertionCertificate $certificate;

    // public ClientAssertion Assertion { get; private set; }

    public Authenticator $authenticator;

    public string $clientId;

    public bool $hasCredential;

    public function addToParameters(array $parameters)
    {
        if (isset($this->clientId))
        {
            $parameters[OAuth2Parameter::CLIENT_ID] = $this->clientId;
        }

        if (isset($this->credential))
        {
            if (isset($this->credential->secureClientSecret))
            {
                $this->credential->secureClientSecret->applyTo($parameters);
            }
            else
            {
                $parameters[OAuth2Parameter::CLIENT_SECRET] = $this->credential->clientSecret;
            }
        }
        else if (isset($this->assertion))
        {
            $parameters[OAuth2Parameter::CLIENT_ASSERTION_TYPE] = $this->assertion->assertionType;
            $parameters[OAuth2Parameter::CLIENT_ASSERTION] = $this->assertion->assertion;
        }
        else if (isset($this->certificate))
        {
            $jwtToken = new JsonWebToken($this->certificate, $this->assertion->selfSignedJwtAudience);
            $clientAssertion = $jwtToken->sign($this->certificate);
            $parameters[OAuth2Parameter::CLIENT_ASSERTION_TYPE] = $clientAssertion.AssertionType;
            $parameters[OAuth2Parameter::CLIENT_ASSERTION] = $clientAssertion.Assertion;
        }
    }
}
