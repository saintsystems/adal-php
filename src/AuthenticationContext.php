<?php

namespace ADAL;

/**
 * Retrieves authentication tokens from Azure Active Directory and ADFS services.
 */
class AuthenticationContext
{
    protected $authenticator;

    /**
     * The authentication authority
     * @var Authority
     */
    protected $authority;

    protected $oauth2client;

    protected $correlationId;

    protected $callContext;

    protected $tokenCache;

    protected $tokenRequestWithUserCode;

    /**
     * Constructor to create the context with the address of the authority.
     */
    public function __construct($authority, $validateAuthority, $tokenCache)
    {
        $validate = ( ! isset($validateAuthority) || $validateAuthority === null || $validateAuthority );

        $this->authenticator = new Authenticator($authority, ($validateAuthority != AuthorityValidationType::FALSE));

        $this->authority = new Authority($authority, $validate);
        $this->oauth2client = null;
        $this->correlationId = null;
        $this->callContext = null;//{ options : globalADALOptions };
        $this->tokenCache = isset($tokenCache) ? $tokenCache : null; //$globalCache;
        $this->tokenRequestWithUserCode = null;// = {};
    }

    /**
     * Gets address of the authority to issue token.
     * @return string
     */
    public function getAuthority()
    {
        return $this->authenticator->getAuthority();
    }

    /**
     * Gets URL of the authorize endpoint including the query parameters.
     *
     * @param string $resource  Identifier of the target resource that is the recipient of the requested token.
     * @param string $redirect_uri  Redirection URI
     * @param array  $extra_parameters  Array of extra parameters like scope or state (Ex: array('scope' => null, 'state' => ''))
     * @return string URL of the authorize endpoint including the query parameters.
     */
    public function getAuthorizationRequestUrl(string $resource, string $clientId, string $redirectUri, array $extraQueryParameters = array())
    {
        $parameters = array_merge(array(
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri
        ), $extraQueryParameters);

        $auth_endpoint = $this->authority->createAuthorityUrl();

        return $auth_endpoint . '?' . http_build_query($parameters, null, '&');
    }

    /**
     * Gets a token for a given resource.
     * @param {string}   $authorizationCode                   An authorization code returned from a client.
     * @param {string}   $redirectUri                         The redirect uri that was used in the authorize call.
     * @param {string}   $resource                            A URI that identifies the resource for which the token is valid.
     * @param {string}   $clientId                            The OAuth client id of the calling application.
     * @param {string}   $clientSecret                        The OAuth client secret of the calling application.
     */
    public function acquireTokenWithAuthorizationCode($authorizationCode, $redirectUri, $resource, $clientId, $clientSecret) {

        // argument.validateStringParameter(resource, 'resource');
        // argument.validateStringParameter(authorizationCode, 'authorizationCode');
        // argument.validateStringParameter(redirectUri, 'redirectUri');
        // argument.validateStringParameter(clientId, 'clientId');

        // $this->acquireToken(callback, function() {
        // var tokenRequest = new TokenRequest(this._callContext, this, clientId, resource, redirectUri);
        // tokenRequest.getTokenWithAuthorizationCode(authorizationCode, clientSecret, callback);
        // });
    }

}
