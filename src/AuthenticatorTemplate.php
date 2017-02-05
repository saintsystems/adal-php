<?php

namespace ADAL;

use GuzzleHttp\Client;

class AuthenticatorTemplate
{
    use TFromJson;

    private const AUTHORIZE_ENDPOINT_TEMPLATE = "https://{host}/{tenant}/oauth2/authorize";
    private const DEVICE_CODE_ENDPOINT_TEMPLATE = "https://{host}/{tenant}/oauth2/devicecode";
    private const METADATA_TEMPLATE = "{\"Host\":\"{host}\", \"Authority\":\"https://{host}/{tenant}/\", \"InstanceDiscoveryEndpoint\":\"https://{host}/common/discovery/instance\", \"DeviceCodeEndpoint\":\"" . self::DEVICE_CODE_ENDPOINT_TEMPLATE . "\", \"AuthorizeEndpoint\":\"" . self::AUTHORIZE_ENDPOINT_TEMPLATE . "\", \"TokenEndpoint\":\"https://{host}/{tenant}/oauth2/token\", \"UserRealmEndpoint\":\"https://{host}/common/UserRealm\"}";

    public static function createFromHost(string $host) //:AuthenticatorTemplate
    {
        $metadata = str_replace('{host}', $host, self::METADATA_TEMPLATE);
        $authority = AuthenticatorTemplate::deserialize($metadata);
        $authority->Issuer = $authority->TokenEndpoint;

        return $authority;
    }

    public $Host;

    public $Issuer;

    public $Authority;

    public $InstanceDiscoveryEndpoint;

    public $DeviceCodeEndpoint;

    public $AuthorizeEndpoint;

    public $TokenEndpoint;

    public $UserRealmEndpoint;

    public function verifyAnotherHostByInstanceDiscovery(string $host, string $tenant, CallState $callState)
    {
        $instanceDiscoveryEndpoint = $this->InstanceDiscoveryEndpoint;
        $instanceDiscoveryEndpoint .= '?api-version=1.0&authorization_endpoint=' . self::AUTHORIZE_ENDPOINT_TEMPLATE;
        $instanceDiscoveryEndpoint = str_replace('{host}', $host, $instanceDiscoveryEndpoint);
        $instanceDiscoveryEndpoint = str_replace('{tenant}', $tenant, $instanceDiscoveryEndpoint);

        try
        {
            $client = new Client();
            $res = $client->get($instanceDiscoveryEndpoint);
            $status = $res->getStatusCode();
            // "200"
            $contentType = $res->getHeader('content-type');
            // 'application/json; charset=utf8'
            $body = $res->getBody();

            Log::get_instance()->debug(__METHOD__ . ': ' . $body);

            $discoveryResponse = json_decode($body, true);
            
            //$discoveryResponse = InstanceDiscoveryResponse::deserialize(json_encode($discoveryResponse));

            // InstanceDiscoveryResponse discoveryResponse = await client.GetResponseAsync<InstanceDiscoveryResponse>().ConfigureAwait(false);

            // if (discoveryResponse.TenantDiscoveryEndpoint == null)
            // {
            //     throw new AdalException(AdalError::AUTHORITY_NOT_IN_VALID_LIST);
            // }
            if ( ! isset($discoveryResponse['tenant_discovery_endpoint']) ) {
            // if ( ! isset($discoveryResponse->tenant_discovery_endpoint) ) {
                throw new AdalException(AdalError::AUTHORITY_NOT_IN_VALID_LIST);
            }
        }
        catch (Exception $ex)
        {
            throw new Exception(($ex.ErrorCode == "invalid_instance") ? AdalError::AUTHORITY_NOT_IN_VALID_LIST : AdalError::AUTHORITY_VALIDATION_FAILED, $ex);
        }
    }

}