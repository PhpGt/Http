<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * The user has sent too many requests in a given amount of time. Intended
 * for use with rate-limiting schemes.
 * @link https://httpstatuses.com/429
 */
class HttpTooManyRequests extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::TOO_MANY_REQUESTS;
	}
}