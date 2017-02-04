<?php

namespace ADAL;

abstract class AADConstants
{
    const WORLD_WIDE_AUTHORITY = 'login.windows.net';
    const WELL_KNOWN_AUTHORITY_HOSTS = [
        'login.windows.net', 
        'login.microsoftonline.com',
        'login.chinacloudapi.cn',
        'login-us.microsoftonline.com',
        'login.microsoftonline.de'
    ];
    const INSTANCE_DISCOVERY_ENDPOINT_TEMPLATE = 'https://{authorize_host}/common/discovery/instance?authorization_endpoint={authorize_endpoint}&api-version=1.0';
    const AUTHORIZE_ENDPOINT_PATH = '/oauth2/authorize';
    const TOKEN_ENDPOINT_PATH = '/oauth2/token'; 
    const DEVICE_ENDPOINT_PATH = '/oauth2/devicecode';
}

