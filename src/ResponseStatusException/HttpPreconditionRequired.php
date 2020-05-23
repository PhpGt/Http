<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpPreconditionRequired extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::PRECONDITION_REQUIRED;
	}
}