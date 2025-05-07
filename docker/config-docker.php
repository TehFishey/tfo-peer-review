<?php

if (!function_exists('setenv')) {
	function setenv($env, $default) {
        if (($val = getenv($env)) !== false) {
			return $val;
		}
		else {
			return $default;
		}
	}
}

if (!function_exists('normalizeProxyHeader')) {
    function normalizeProxyHeader($header) {
        $header = strtoupper($header);
        $header = str_replace('-', '_', $header);
        if (0 !== strpos($header, 'HTTP_')) {
            $header = 'HTTP_' . $header;
        }
        return $header;
    }
}
/** Configuration Variables **/

define('USING_PROXY', setenv('PROXY_EXISTS', false));
define('PROXY_IP', gethostbyname(setenv('PROXY_HOST','localhost')));
define('PROXY_HEADER', normalizeProxyHeader(setenv('PROXY_HEADER','X-Forwarded-For')));

define('DB_NAME', setenv('TPR_DB_NAME','tfopeerreview_db'));
define('DB_USERNAME', setenv('TPR_DB_USER','tfopeerreview_user'));
define('DB_PASSWORD', setenv('TPR_DB_PASSWORD','password'));
define('DB_HOST', setenv('TPR_DB_HOST','localhost'));

define('API_PATH', setenv('TPR_API_URL','https://finaloutpost.net/usersc/plugins/apibuilder/examples/labLoad.php'));
define('API_KEY', setenv('TPR_API_KEY','YOUR-API-CODE-HERE'));