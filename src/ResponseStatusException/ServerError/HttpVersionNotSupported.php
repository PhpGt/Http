<?php
namespace Gt\Http\ResponseStatusException\ServerError;

use Gt\Http\StatusCode;

/**
 * The server does not support the HTTP protocol version used in the request.
 * @link https://httpstatuses.com/505
 */
class HttpVersionNotSupported extends ServerErrorException {
	public function getHttpCode():int {
		return StatusCode::HTTP_VERSION_NOT_SUPPORTED;
	}
}
