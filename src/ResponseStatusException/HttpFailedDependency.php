<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpFailedDependency extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::FAILED_DEPENDENCY;
	}
}