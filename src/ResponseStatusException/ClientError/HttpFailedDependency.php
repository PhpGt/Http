<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpFailedDependency extends ResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::FAILED_DEPENDENCY;
	}
}
