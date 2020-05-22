<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpExpectationFailed extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::EXPECTATION_FAILED;
	}
}