<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * This and all future requests should be directed to the given URI.
 * @link https://httpstatuses.com/301
 */
class HttpMovedPermanently extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::MOVED_PERMANENTLY;
	}
}