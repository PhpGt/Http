<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * The server was acting as a gateway or proxy and did not receive a timely
 * response from the upstream server.
 * @link https://httpstatuses.com/504
 */
class HttpGatewayTimeout extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::GATEWAY_TIMEOUT;
	}
}