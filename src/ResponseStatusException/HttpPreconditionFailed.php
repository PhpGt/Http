<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpPreconditionFailed extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::PRECONDITION_FAILED;
	}
}