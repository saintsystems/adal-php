<?php

namespace ADAL;

abstract class AADConstants
{
    const WORLD_WIDE_AUTHORITY = 'login.windows.net';
    const WELL_KNOWN_AUTHORITY_HOSTS = [
        'login.windows.net',            // Microsoft Azure Worldwide - Used in validation scenarios where host is not this list 
        'login.microsoftonline.com',    // Microsoft Azure Worldwide
        'login.chinacloudapi.cn',       // Microsoft Azure China
        'login-us.microsoftonline.com', // Microsoft Azure US Government
        'login.microsoftonline.de'      // Microsoft Azure US Germany
    ];
    const INSTANCE_DISCOVERY_ENDPOINT_TEMPLATE = 'https://{authorize_host}/common/discovery/instance?authorization_endpoint={authorize_endpoint}&api-version=1.0';
    const AUTHORIZE_ENDPOINT_PATH = '/oauth2/authorize';
    const TOKEN_ENDPOINT_PATH = '/oauth2/token'; 
    const DEVICE_ENDPOINT_PATH = '/oauth2/devicecode';
}

