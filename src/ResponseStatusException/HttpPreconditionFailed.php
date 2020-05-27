<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpPreconditionFailed extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::PRECONDITION_FAILED;
	}
}