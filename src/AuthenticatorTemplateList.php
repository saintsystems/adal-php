<?php

namespace ADAL;

class AuthenticatorTemplateList
{
    const TRUSTED_HOST_LIST = [
        'login.windows.net',            // Microsoft Azure Worldwide - Used in validation scenarios where host is not this list 
        'login.microsoftonline.com',    // Microsoft Azure Worldwide
        'login.chinacloudapi.cn',       // Microsoft Azure China
        'login-us.microsoftonline.com', // Microsoft Azure US Government
        'login.microsoftonline.de'      // Microsoft Azure US Germany
    ];

    private $authenticatorTemplateList;

    public function __construct()
    {
        $this->authenticatorTemplateList = array();
        foreach (self::TRUSTED_HOST_LIST as $host) {
            $this->authenticatorTemplateList[$host] = AuthenticatorTemplate::createFromHost($host);
        }
    }

    public function findMatchingItem(bool $validateAuthority, string $host, string $tenant, CallState $callState) //: AuthenticatorTemplate
        {
            $matchingAuthenticatorTemplate = null;
            if ($validateAuthority)
            {
                // $matchingAuthenticatorTemplate = this.FirstOrDefault(a => string.Compare($host, a.Host, StringComparison.OrdinalIgnoreCase) == 0);
                $matchingAuthenticatorTemplate = null;//$this->authenticatorTemplateList[$host];
                if ($matchingAuthenticatorTemplate == null)
                {
                    // We only check with the first trusted authority (login.windows.net) for instance discovery
                    $first = current($this->authenticatorTemplateList);
                    $first->verifyAnotherHostByInstanceDiscovery($host, $tenant, $callState);
                    //await this.First().verifyAnotherHostByInstanceDiscovery($host, $tenant, $callState).ConfigureAwait(false);
                }
            }

            return $matchingAuthenticatorTemplate ?? AuthenticatorTemplate::createFromHost($host);
        }
}

