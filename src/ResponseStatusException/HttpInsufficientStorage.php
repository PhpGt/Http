<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * The server is unable to store the representation needed to complete the
 * request.
 * @link https://httpstatuses.com/507
 */
class HttpInsufficientStorage extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::INSUFFICIENT_STORAGE;
	}
}