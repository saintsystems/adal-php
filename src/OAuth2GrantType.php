<?php

namespace ADAL;

abstract class OAuth2GrantType
{
    const AUTHORIZATION_CODE = 'authorization_code';
    const REFRESH_TOKEN = 'refresh_token';
    const CLIENT_CREDENTIALS = 'client_credentials';
    const JWT_BEARER = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';
    const PASSWORD = 'password';
    const SAML1 = 'urn:ietf:params:oauth:grant-type:saml1_1-bearer';
    const SAML2 = 'urn:ietf:params:oauth:grant-type:saml2-bearer';
    const DEVICE_CODE = 'device_code';
}
