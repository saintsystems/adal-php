<?php

use ADAL\Authority;
use ADAL\InvalidAuthorityUrlException;
use PHPUnit\Framework\TestCase;

class AuthorityTest extends TestCase
{

    /**
     * Throw Exception when insecure scheme (http:) is used in authorityUrl
     * @return [type] [description]
     */
    public function testInvalidScheme()
    {
        $this->expectException( InvalidAuthorityUrlException::class );
        $authorityUrl = 'http://login.windows.net/common/';
        $authority = new Authority($authorityUrl, false);
    }

    /**
     * Throw Exception when querystring is specified in the authorityUrl
     * @return [type] [description]
     */
    public function testInvalidQueryInAuthorityUrl()
    {
        $this->expectException( InvalidAuthorityUrlException::class );
        $authorityUrl = 'https://login.windows.net/common/?foo=bar';
        $authority = new Authority($authorityUrl, false);
    }

    /**
     * Throw Exception when no tenant is specified in the authorityUrl
     * @return [type] [description]
     */
    public function testInvalidTenant()
    {
        $this->expectException( InvalidAuthorityUrlException::class );
        $authorityUrl = 'https://login.windows.net/';
        $authority = new Authority($authorityUrl, false);
    }

}
