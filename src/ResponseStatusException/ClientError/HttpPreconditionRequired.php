<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

class HttpPreconditionRequired extends ClientErrorException {
	public function getHttpCode():int {
		return StatusCode::PRECONDITION_REQUIRED;
	}
}
