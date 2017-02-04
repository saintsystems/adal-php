<?php

namespace ADAL;

use GuzzleHttp\Client;

class Authority
{
    /**
     * [$log description]
     * @var [type]
     */
    private $log;

    /**
     * The URL of the authority
     * @var string
     */
    private $authorityUrl;

    /**
     * The parsed authority URL
     * @var array
     */
    private $url;

    /**
     * [$validated description]
     * @var bool
     */
    private $validated;

    /**
     * The token endpoint that the authority uses as discovered by instance discovery.
     * @var string
     */
    private $authorizationEndpoint;

    /**
     * The token endpoint that the authority uses as discovered by instance discovery.
     * @var string
     */
    private $tokenEndpoint;

    /**
     * The device code endpoint that the authority uses as discovered by instance discovery.
     * @var string
     */
    private $deviceCodeEndpoint;

    /**
     * [$host description]
     * @var string
     */
    private $host;

    /**
     * [$tenant description]
     * @var string
     */
    private $tenant;

    /**
     * [$isAdfsAuthority description]
     * @var bool
     */
    private $isAdfsAuthority;

    /**
     * Instantiates a new instance of the Authority class.
     * @param string $authorityUrl      [description]
     * @param bool $validateAuthority [description]
     */
    public function __construct(string $authorityUrl, bool $validateAuthority)
    {
        $this->log = null;
        $this->authorityUrl = $authorityUrl;
        $this->url = parse_url($authorityUrl);
        $this->validateAuthorityUrl();

        $this->validated = !$validateAuthority;
        $this->host = null;
        $this->tenant = null;
        $this->parseAuthority();

        $this->authorizationEndpoint = null;
        $this->tokenEndpoint = null;
        $this->deviceCodeEndpoint = null;
        $this->isAdfsAuthority = (strtolower($this->tenant) === "adfs");
    }

    /**
     * Returns the authority url
     * @return string
     */
    public function getAuthorityUrl()
    {
        return $this->authorityUrl;
    }

    /**
     * Returns the token endpoint
     * @return string
     */
    public function getTokenEndpoint()
    {
        return $this->tokenEndpoint;
    }

    /**
     * Returns the device code endpoint
     * @return string
     */
    public function getDeviceCodeEndpoint()
    {
        return $this->deviceCodeEndpoint;
    }

    /**
     * Returns a valid authority url
     * @return string
     */
    public function createAuthorityUrl()
    {
        return 'https://' . $this->url['host'] . '/' . $this->tenant . AADConstants::AUTHORIZE_ENDPOINT_PATH;
    }

    /**
     * Checks the authority url to ensure that it meets basic requirements such as being over SSL. 
     * If it does not then this method will throw if any of the checks fail.
     */
    private function validateAuthorityUrl()
    {
        if ($this->url['scheme'] !== 'https') {
            throw new InvalidAuthorityUrlException('The authority url must be an https endpoint.');
        }

        if (isset($this->url['query'])) {
            throw new InvalidAuthorityUrlException('The authority url must not have a query string.');
        }
    }

    /**
     * Parse the authority to get the tenant name.  The rest of the
     * URL is thrown away in favor of one of the endpoints from the validation doc.
     * @return void
     */
    private function parseAuthority()
    {
        $this->host = $this->url['host'];

        $pathParts = explode('/', $this->url['path']);
        $this->tenant = $pathParts[1];

        if (!$this->tenant) {
            throw new InvalidAuthorityUrlException('Could not determine tenant.');
        }
    }

    /**
     * Performs instance discovery based on a simple match against well known authorities.
     * @return [bool] Returns true if the authority is recognized.
     */
    private function performStaticInstanceDiscovery()
    {
        //$this->log->verbose('Performing static instance discovery');

        $found = in_array($this->url['host'], AADConstants::WELL_KNOWN_AUTHORITY_HOSTS);

        // if ($found) {
        //     $this->log->verbose('Authority validated via static instance discovery.');
        // }

        return found;
    }

    /**
     * Creates an instance discovery endpoint url for the specific authority that this object represents.
     * @private
     * @param  {string} authorityHost The host name of a well known authority.
     * @return {URL}    The constructed endpoint url.
     */
    private function createInstanceDiscoveryEndpointFromTemplate($authorityHost)
    {
        $discoveryEndpoint = AADConstants::INSTANCE_DISCOVERY_ENDPOINT_TEMPLATE;
        $discoveryEndpoint = str_replace('{authorize_host}', $authorityHost, $discoveryEndpoint);
        $discoveryEndpoint = str_replace('{authorize_endpoint}', encodeURIComponent($this->createAuthorityUrl()), $discoveryEndpoint);
        return parse_url($discoveryEndpoint);
    }

    /**
     * Performs instance discovery via a network call to well known authorities.
     * @private
     * @param {Authority.InstanceDiscoveryCallback}   callback    The callback function.  If succesful,
     *                                                            this function calls the callback with the
     *                                                            tenantDiscoveryEndpoint returned by the
     *                                                            server.
     */
    private function performDynamicInstanceDiscovery()
    {
        $discoveryEndpoint = $this->createInstanceDiscoveryEndpointFromTemplate(AADConstants::WORLD_WIDE_AUTHORITY);

        //$this->log->verbose('Attempting instance discover at: ' + url.format(discoveryEndpoint));
        $client = new Client;
        $res = $client->get($discoveryEndpoint);
        $status = $res->getStatusCode();
        // "200"
        $contentType = $res->getHeader('content-type');
        // 'application/json; charset=utf8'
        $body = $res->getBody();

        $discoveryResponse = json_decode($body, true);

        if ($tenantDiscoveryEndpoint = $discoveryResponse['tenant_discovery_endpoint']) {
            return true; //$tenantDiscoveryEndpoint;
        } else {
            //throw new \Exception('Failed to parse instance discovery response');
            //$this->log->error('Failed to parse instance discovery response');
            return false;
        }
    }

    /**
     * @callback InstanceDiscoveryCallback
     * @private
     * @memberOf Authority
     * @param {Error} err If an error occurs during instance discovery then it will be returned here.
     * @param {string} tenantDiscoveryEndpoint If instance discovery is successful then this will contain the
     *                                         tenantDiscoveryEndpoint associated with the authority.
     */

    /**
     * Determines whether the authority is recognized as a trusted AAD authority.
     * @private
     * @param {Authority.InstanceDiscoveryCallback}   callback    The callback function.
     */
    private function validateViaInstanceDiscovery() {
        if ($this->performStaticInstanceDiscovery()) {
            return true;
        }
        return $this->performDynamicInstanceDiscovery();
    }

    /**
     * Given a tenant discovery endpoint this method will attempt to discover the token endpoint.  If the
     * tenant discovery endpoint is unreachable for some reason then it will fall back to a algorithmic generation of the
     * token endpoint url.
     * @private
     * @param {string}           tenantDiscoveryEndpoint   The url of the tenant discovery endpoint for this authority.
     */
    private function getOAuthEndpoints($tenantDiscoveryEndpoint)
    {
        if (isset($this->tokenEndpoint) && isset($this->deviceCodeEndpoint)) {
            return;
        } else {
            // fallback to the well known token endpoint path.
            if ( ! isset($this->tokenEndpoint)) {
                $this->tokenEndpoint = 'https://' . $this->url['host'] . '/' . $this->tenant . AADConstants::TOKEN_ENDPOINT_PATH;
            }

            if ( ! isset($this->deviceCodeEndpoint)) {
               $this->deviceCodeEndpoint = 'https://' . $this->url['host'] . '/' . $this->tenant . AADConstants::DEVICE_ENDPOINT_PATH;
            }

            return;
        }
    }

    /**
     * Perform validation on the authority represented by this object.  In addition to simple validation
     * the oauth token endpoint will be retrieved.
     * @param {Authority.ValidateCallback}   callback   The callback function.
     */
    public function validate($callContext)
    {
        //$this->log = new Logger('Authority', $callContext->logContext);
        //$this->callContext = $callContext;

        if ( ! $this->validated ) {
            //$this->log->verbose('Performing instance discovery: ' . $this->getAuthorityUrl);
            if ($this->validateViaInstanceDiscovery()) {
                $this->validated = true;
                $this->getOAuthEndpoints();
                return;
            } 
        } else {
            //$this->log->verbose('Instance discovery/validation has either already been completed or is turned off: ' . $this->getAuthorityUrl;
            $this->getOAuthEndpoints();
            return;
        }

    }

}
