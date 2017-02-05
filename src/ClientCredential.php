<?php

namespace ADAL;

/**
 * Credential including client id and secret.
 */
final class ClientCredential
{
    /**
     * Constructor to create credential with client id and secret
     * @param  string $clientId     Identifier of the client requesting the token.
     * @param  string $clientSecret Secret of the client requesting the token.
     * @return ClientCredential
     */
    public function __constructs(string $clientId, string $clientSecret)
    {
        if (empty($clientId))
        {
            throw new ArgumentNullException('$clientId');
        }

        if (empty($clientSecret))
        {
            throw new ArgumentNullException('$clientSecret');
        }

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /// <summary>
    /// Gets the identifier of the client requesting the token.
    /// </summary>
    public function getClientId()
    {
        return $this->clientId;
    }

    protected function getClientSecret()
    {
        return $this->clientSecret;
    }
}
