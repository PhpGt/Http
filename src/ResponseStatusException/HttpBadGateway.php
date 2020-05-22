<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * The server was acting as a gateway or proxy and received an invalid response
 * from the upstream server.
 * @link https://httpstatuses.com/502
 */
class HttpBadGateway extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::BAD_GATEWAY;
	}
}