<?php

namespace ADAL;

/**
 * Contains the results of one token acquisition operation. 
 */
final class AuthenticationResult
{
    private const OAUTH2_AUTHORIZATION_HEADER = 'Bearer ';

    private $accessTokenType;

    private $accessToken;

    private $expiresOn;

    private $tenantId;

    private $idToken;

    /**
     * Creates result returned from AcquireToken. Except in advanced scenarios related to token caching, you do not need to create any instance of AuthenticationResult.
     * @param string $accessTokenType Type of the Access Token returned
     * @param string $accessToken     The Access Token requested
     * @param [type] $expiresOn       The point in time in which the Access Token returned in the AccessToken property ceases to be valid
     */
    public function __construct(string $accessTokenType, string $accessToken, $expiresOn)
    {
        $this->accessTokenType = $accessTokenType;
        $this->accessToken = $accessToken;
        $this->expiresOn = $expiresOn;//DateTime.SpecifyKind($expiresOn.DateTime, DateTimeKind.Utc);
        //this.ExtendedExpiresOn = DateTime.SpecifyKind(expiresOn.DateTime, DateTimeKind.Utc);
    }

    /**
     * Gets the type of the Access Token returned.
     * @return string
     */
    public function getAccessTokenType()
    {
        return $this->accessTokenType;
    }

    /**
     * Gets the Access Token requested.
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Gets the point in time in which the Access Token returned in the AccessToken property ceases to be valid.
     * This value is calculated based on current UTC time measured locally and the value expiresIn received from the service.
     * @return date
     */
    public function getExpiresOn()
    {
        return $this->expiresOn;
    }

    /// <summary>
    /// Gets the point in time in which the Access Token returned in the AccessToken property ceases to be valid in ADAL's extended LifeTime.
    /// This value is calculated based on current UTC time measured locally and the value ext_expiresIn received from the service.
    /// </summary>
    // internal DateTimeOffset ExtendedExpiresOn { get; set; }

    /// <summary>
    /// Gives information to the developer whether token returned is during normal or extended lifetime.
    /// </summary>
    // [DataMember]
    // public bool ExtendedLifeTimeToken { get; internal set; }

    /**
     * Gets an identifier for the tenant the token was acquired from. This property will be null if tenant information is not returned by the service.
     * @return string
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /// <summary>
    /// Gets user information including user Id. Some elements in UserInfo might be null if not returned by the service.
    /// </summary>
    //[DataMember]
    //public UserInfo UserInfo { get; internal set; }

    /**
     * Gets the entire Id Token if returned by the service or null if no Id Token is returned.
     * @return string
     */
    public function getIdToken()
    {
        return $this->idToken;
    }

    /**
     * Creates authorization header from authentication result.
     * @return string  Created authorization header
     */
    public function createAuthorizationHeader() //:string
    {
        return self::OAUTH2_AUTHORIZATION_HEADER . $this->accessToken;
    }

}