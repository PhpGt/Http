<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

class HttpPreconditionFailed extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::PRECONDITION_FAILED;
	}
}