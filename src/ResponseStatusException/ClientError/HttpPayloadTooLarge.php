<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

class HttpPayloadTooLarge extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::PAYLOAD_TOO_LARGE;
	}
}