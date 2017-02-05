<?php

namespace ADAL;

use InvalidArgumentException;

class Authenticator
{
    private const TENANTLESS_TENANT_NAME = 'Common';

    private static $authenticatorTemplateList;

    private $updatedFromTemplate; 

    /**
     * The URL of the authority
     * @var string
     */
    private $authority;

    /**
     * The parsed authority URL
     * @var array
     */
    private $url;

    /**
     * [$validateAuthority description]
     * @var bool
     */
    private $validateAuthority;

    /**
     * [$isTenantLess description]
     * @var bool
     */
    private $isTenantLess;

    /**
     * [$authorizationUri description]
     * @var [type]
     */
    private $authorizationUri;

    /**
     * [$deviceCodeUri description]
     * @var [type]
     */
    private $deviceCodeUri;

    /**
     * [$tokenUri description]
     * @var [type]
     */
    private $tokenUri;

    /**
     * [$userRealmUri description]
     * @var [type]
     */
    private $userRealmUri;

    /**
     * [$selfSignedJwtAudience description]
     * @var [type]
     */
    private $selfSignedJwtAudience;

    /**
     * [$correlationId description]
     * @var [type]
     */
    private $correlationId;

    /**
     * Instantiates a new instance of the Authority class.
     * @param string $authorityUrl      [description]
     * @param bool $validateAuthority [description]
     */
    public function __construct(string $authority, bool $validateAuthority)
    {
        self::$authenticatorTemplateList = new AuthenticatorTemplateList();
        $this->authority = self::canonicalizeUri($authority);

        $this->authorityType = self::detectAuthorityType($this->getAuthority());

        if ($this->getAuthorityType() != AuthorityType::AAD && $validateAuthority)
        {
            throw new InvalidArgumentException(AdalErrorMessage::UNSUPPORTED_AUTHORITY_VALIDATION . ': $validateAuthority');
        }

        $this->validateAuthority = $validateAuthority;
    }

    /**
     * Returns the authority url
     * @return string
     */
    public function getAuthority()
    {
        return $this->authority;
    }

    /**
     * Returns the authority url
     * @return AuthorityType
     */
    public function getAuthorityType()
    {
        return $this->authorityType;
    }

    /**
     * getValidateAuthority()
     * @return bool
     */
    public function getValidateAuthority()
    {
        return $this->validateAuthority;
    }

    /**
     * getIsTenantless()
     * @return bool
     */
    public function getIsTenantless()
    {
        return $this->isTenantLess;
    }

    public function getAuthorizationUri()
    {
        return $this->authorizationUri;
    }

    public function getDeviceCodeUri()
    {
        return $this->deviceCodeUri;
    }

    public function getTokenUri()
    {
        return $this->tokenUri;
    }

    public function getUserRealmUri()
    {
        return $this->userRealmUri;
    }

    public function getSelfSignedJwtAudience()
    {
        return $this->selfSignedJwtAudience;
    }

    public function getCorrelationId()
    {
        return $this->correlationId;
    }

    public function updateFromTemplate(CallState $callState)
    {
        if ( ! $this->updatedFromTemplate) {
            $authorityUri = parse_url($this->getAuthority());
            $host = $authorityUri['host'];
            $path = $authorityUri['path'];
            $pathParts = explode('/', $path);
            $tenant = $pathParts[1];

            $matchingTemplate = self::$authenticatorTemplateList->findMatchingItem($this->getValidateAuthority(), $host, $tenant, $callState);

            $this->authorizationUri = str_replace('{tenant}', $tenant, $matchingTemplate->AuthorizeEndpoint);
            $this->deviceCodeUri = str_replace('{tenant}', $tenant, $matchingTemplate->DeviceCodeEndpoint);
            $this->tokenUri = str_replace('{tenant}', $tenant, $matchingTemplate->TokenEndpoint);
            $this->userRealmUri = self::canonicalizeUri($matchingTemplate->UserRealmEndpoint);
            $this->isTenantless = strcmp($tenant, self::TENANTLESS_TENANT_NAME) === 0;
            $this->selfSignedJwtAudience = str_replace('{tenant}', $tenant, $matchingTemplate->Issuer);
            $this->updatedFromTemplate = true;
        }
    }

    public function updateTenantId(string $tenantId)
    {
        if ($this->getIsTenantless() && ! empty($tenantId))
        {
            $this->replaceTenantlessTenant($tenantId);
            $this->updatedFromTemplate = false;
        }
    }

    private static function detectAuthorityType(string $authority) //: AuthorityType
    {
        if (empty($authority))
        {
            throw new ArgumentNullException('authority');
        }

        try {
            $authorityUri = parse_url($authority);
        } catch (Exception $e) {
            throw new InvalidArgumentException(AdalErrorMessage::AUTHORITY_INVALID_URI_FORMAT . ': ($authority = "' . $authority . '")');
        }

        if ($authorityUri['scheme'] != 'https')
        {
            throw new InvalidArgumentException(AdalErrorMessage::AUTHORITY_URI_INSECURE . ': ($authority = "' . $authority . '")');
        }

        $path = substr($authorityUri['path'], 1);
        if (empty($path))
        {
            throw new InvalidArgumentException(AdalErrorMessage::AUTHORITY_URI_INVALID_PATH . ': ($authority = "' . $authority . '")');
        }

        $firstPath = substr($path, 0, strpos($path, '/'));
        $authorityType = self::isAdfsAuthority($firstPath) ? AuthorityType::ADFS : AuthorityType::AAD;

        return $authorityType;
    }

    private static function canonicalizeUri(string $uri) //: string
    {
        if ( ! empty($uri) && ! ends_with($uri, '/'))
        {
            $uri = $uri . '/';
        }

        return $uri;
    }

    private static function isAdfsAuthority(string $firstPath) //: bool
    {
        return strcmp($firstPath, 'adfs') === 0;
    }

    private function replaceTenantlessTenant(string $tenantId)
    {
        //TODO: Replace with PHP preg_replace
        $regex = new Regex(Regex.Escape(self::TENANTLESS_TENANT_NAME), RegexOptions.IgnoreCase);
        $this->authority = regex.Replace($this->getAuthority(), $tenantId, 1);
    }

}
