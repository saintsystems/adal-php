<?php

namespace ADAL;

/**
 * Interface for implementing certificate based operations
 */
interface IClientAssertionCertificate
{
    /**
     * Signs a message using the private key in the certificate
     * @param  string $message Message that needs to be signed
     * @return [type]          Signed message as a byte array
     */
    public function sign(string $message); //byte[] 

    /**
     * Gets the identifier of the client requesting the token.
     */
    public string $clientId;

    /**
     * Thumbprint of the Certificate
     */
    public string $thumbprint;
}