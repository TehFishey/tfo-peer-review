<?php
/**
 * @see       https://github.com/zendframework/zend-http for the canonical source repository
 * @copyright Copyright (c) 2005-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-http/blob/master/LICENSE.md New BSD License
 */
#namespace Zend\Http\PhpEnvironment;
include_once (__DIR__).'/../../config/config.php';

/**
 * Functionality for determining client IP address.
 */
class RemoteAddress
{
    /**
     * Whether to use proxy addresses or not.
     *
     * As default this setting is disabled - IP address is mostly needed to increase
     * security. HTTP_* are not reliable since can easily be spoofed. It can be enabled
     * just for more flexibility, but if user uses proxy to connect to trusted services
     * it's his/her own risk, only reliable field for IP address is $_SERVER['REMOTE_ADDR'].
     *
     * @var bool
     */
    protected $useProxy = USING_PROXY;

    /**
     * List of trusted proxy IP addresses
     *
     * @var array
     */
    protected $trustedProxies = [PROXY_IP];

    /**
     * HTTP header to introspect for proxies
     *
     * @var string
     */
    protected $proxyHeader = PROXY_HEADER;

    /**
     * Changes proxy handling setting.
     *
     * This must be static method, since validators are recovered automatically
     * at session read, so this is the only way to switch setting.
     *
     * @param  bool  $useProxy Whether to check also proxied IP addresses.
     * @return $this
     */
    public function setUseProxy($useProxy = true)
    {
        $this->useProxy = $useProxy;
        return $this;
    }

    /**
     * Checks proxy handling setting.
     *
     * @return bool Current setting value.
     */
    public function getUseProxy()
    {
        return $this->useProxy;
    }

    /**
     * Set list of trusted proxy addresses
     *
     * @param  array $trustedProxies
     * @return $this
     */
    public function setTrustedProxies(array $trustedProxies)
    {
        $this->trustedProxies = $trustedProxies;
        return $this;
    }

    /**
     * Set the header to introspect for proxy IPs
     *
     * @param  string $header
     * @return $this
     */
    public function setProxyHeader($header = 'X-Forwarded-For')
    {
        $this->proxyHeader = $this->normalizeProxyHeader($header);
        return $this;
    }

    /**
     * Returns client IP address.
     *
     * @return string IP address.
     */
    public function getIpAddress()
    {
        $ip = $this->getIpAddressFromProxy();
        if ($ip) {
            return $ip;
        }

        // direct IP address
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '';
    }

    /**
     * Attempt to get the IP address for a proxied client
     *
     * @see http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.2
     * @return false|string
     */
    protected function getIpAddressFromProxy()
    {
        if (! $this->useProxy
            || (isset($_SERVER['REMOTE_ADDR']) && ! in_array($_SERVER['REMOTE_ADDR'], $this->trustedProxies))
        ) {
            return false;
        }

        $header = $this->proxyHeader;
        if (! isset($_SERVER[$header]) || empty($_SERVER[$header])) {
            return false;
        }
        
        // Extract IPs
        $ips = explode(',', $_SERVER[$header]);
        // trim, so we can compare against trusted proxies properly
        $ips = array_map('trim', $ips);
        // remove trusted proxy IPs
        $ips = array_diff($ips, $this->trustedProxies);

        // Any left?
        if (empty($ips)) {
            return false;
        }

        // Since we've removed any known, trusted proxy servers, the right-most
        // address represents the first IP we do not know about -- i.e., we do
        // not know if it is a proxy server, or a client. As such, we treat it
        // as the originating IP.
        // @see http://en.wikipedia.org/wiki/X-Forwarded-For
        $ip = array_pop($ips);
        return $ip;
    }

    /**
     * Normalize a header string
     *
     * Normalizes a header string to a format that is compatible with
     * $_SERVER
     *
     * @param  string $header
     * @return string
     */
    protected function normalizeProxyHeader($header)
    {
        $header = strtoupper($header);
        $header = str_replace('-', '_', $header);
        if (0 !== strpos($header, 'HTTP_')) {
            $header = 'HTTP_' . $header;
        }
        return $header;
    }
}
