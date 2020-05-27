<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpPayloadTooLarge extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::PAYLOAD_TOO_LARGE;
	}
}