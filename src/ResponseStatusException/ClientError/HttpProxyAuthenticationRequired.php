<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

/**
 * The client must first authenticate itself with the proxy.
 * @linkhttps://httpstatuses.com/407
 */
class HttpProxyAuthenticationRequired extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::PROXY_AUTHENTICATION_REQUIRED;
	}
}