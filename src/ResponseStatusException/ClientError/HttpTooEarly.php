<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

/**
 * Indicates that the server is unwilling to risk processing a request that
 * might be replayed.
 * @link https://tools.ietf.org/html/rfc8470
 */
class HttpTooEarly extends ClientErrorException {
	public function getHttpCode():int {
		return StatusCode::TOO_EARLY;
	}
}
