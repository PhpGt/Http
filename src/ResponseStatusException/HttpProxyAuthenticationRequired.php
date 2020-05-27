<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * The client must first authenticate itself with the proxy.
 * @linkhttps://httpstatuses.com/407
 */
class HttpProxyAuthenticationRequired extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::PROXY_AUTHENTICATION_REQUIRED;
	}
}