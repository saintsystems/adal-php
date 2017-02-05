<?php

use ADAL\Authenticator;
use ADAL\CallState;
use ADAL\InvalidAuthorityUrlException;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase
{

    /**
     * Throw Exception when insecure scheme (http:) is used in authorityUrl
     * @return [type] [description]
     */
    public function testInvalidScheme()
    {
        $this->expectException( InvalidArgumentException::class );
        $authority = 'http://login.windows.net/common/';
        $validateAuthority = false;
        $authenticator = new Authenticator($authority, $validateAuthority);
    }

    /**
     * Throw Exception when no tenant is specified in the authorityUrl
     * @return [type] [description]
     */
    public function testEmptyPath()
    {
        $this->expectException( InvalidArgumentException::class );
        $authority = 'https://login.windows.net/';
        $validateAuthority = false;
        $authenticator = new Authenticator($authority, $validateAuthority);
    }

    /**
     * Throw Exception when no tenant is specified in the authorityUrl
     * @return [type] [description]
     */
    public function testUpdateFromTemplate()
    {
        //$this->expectException( InvalidArgumentException::class );
        $authority = 'https://login.windows.net/common/';
        $validateAuthority = true;
        $authenticator = new Authenticator($authority, $validateAuthority);
        $callState = new CallState(12345);
        $authenticator->updateFromTemplate($callState);
    }

}
