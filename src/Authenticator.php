<?php

namespace ADAL;

use InvalidArgumentException;

class Authenticator
{
    private const TENANTLESS_TENANT_NAME = 'Common';

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
     * Instantiates a new instance of the Authority class.
     * @param string $authorityUrl      [description]
     * @param bool $validateAuthority [description]
     */
    public function __construct(string $authority, bool $validateAuthority)
    {
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

    /**
     * Parse the authority to get the tenant name.  The rest of the
     * URL is thrown away in favor of one of the endpoints from the validation doc.
     * @return void
     */
    // private function detectAuthorityType()
    // {
    //     $this->host = $this->url['host'];

    //     $pathParts = explode('/', $this->url['path']);
    //     $this->tenant = $pathParts[1];

    //     if (!$this->tenant) {
    //         throw new InvalidAuthorityUrlException('Could not determine tenant.');
    //     }
    // }

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
        $regex = new Regex(Regex.Escape(self::TENANTLESS_TENANT_NAME), RegexOptions.IgnoreCase);
        $this->authority = regex.Replace($this->getAuthority(), $tenantId, 1);
    }

}
