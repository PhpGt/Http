<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpPreconditionRequired extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::PRECONDITION_REQUIRED;
	}
}