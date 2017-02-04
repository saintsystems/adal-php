<?php

use ADAL\AuthenticationContext;
use ADAL\Log;
use PHPUnit\Framework\TestCase;

class AuthenticationContextTest extends TestCase
{
    private $log;

    public function __construct() {
        $this->log = Log::get_instance();
    }

    /**
     * Test retrieving the authorization URL from the AuthenticationContext
     * @return string
     */
    public function testRetrieveAuthorizationUrl()
    {
        $authorityUrl = 'https://login.windows.net/common/';
        $validateAuthority = false;
        $tokenCache = null;
        $auth = new AuthenticationContext($authorityUrl, $validateAuthority, $tokenCache);

        $resource = 'https://saintsystems.crm.dynamics.com';
        $client_id = 'd870c0ff-778a-4b60-b7eb-6f0627ef89d9';
        $redirect_uri = 'https://www.saintsystems.com/gfdcrm/oauth2/callback';

        $authorizeUrl = $auth->getAuthorizationRequestUrl($resource, $client_id, $redirect_uri);
        $expected = 'https://login.windows.net/common/oauth2/authorize?response_type=code&client_id=d870c0ff-778a-4b60-b7eb-6f0627ef89d9&redirect_uri=https%3A%2F%2Fwww.saintsystems.com%2Fgfdcrm%2Foauth2%2Fcallback';

        $this->log->info($authorizeUrl);

        $this->assertEquals($authorizeUrl, $expected);
    }

    /**
     * Test retrieving the authorization URL with additional parameters from the AuthenticationContext
     * @return string
     */
    public function testRetrieveAuthorizationUrlWithParams()
    {
        $authorityUrl = 'https://login.windows.net/common/';
        $validateAuthority = false;
        $tokenCache = null;
        $auth = new AuthenticationContext($authorityUrl, $validateAuthority, $tokenCache);

        $resource = 'https://saintsystems.crm.dynamics.com';
        $client_id = 'd870c0ff-778a-4b60-b7eb-6f0627ef89d9';
        $redirect_uri = 'https://www.saintsystems.com/gfdcrm/oauth2/callback';
        $extra_params = [
            'state' => 'state',
            'scope' => 'UserProfile.Read'
        ];

        $authorizeUrl = $auth->getAuthorizationRequestUrl($resource, $client_id, $redirect_uri, $extra_params);
        $expected = 'https://login.windows.net/common/oauth2/authorize?response_type=code&client_id=d870c0ff-778a-4b60-b7eb-6f0627ef89d9&redirect_uri=https%3A%2F%2Fwww.saintsystems.com%2Fgfdcrm%2Foauth2%2Fcallback&state=state&scope=UserProfile.Read';

        $this->log->info($authorizeUrl);

        $this->assertEquals($authorizeUrl, $expected);
    }

}
