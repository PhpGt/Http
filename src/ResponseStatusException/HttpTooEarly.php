<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * Indicates that the server is unwilling to risk processing a request that
 * might be replayed.
 * @link https://tools.ietf.org/html/rfc8470
 */
class HttpTooEarly extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::TOO_EARLY;
	}
}