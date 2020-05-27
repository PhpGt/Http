<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * Further extensions to the request are required for the server to fulfil it.
 * @link https://httpstatuses.com/510
 */
class HttpNotExtended extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::NOT_EXTENDED;
	}
}