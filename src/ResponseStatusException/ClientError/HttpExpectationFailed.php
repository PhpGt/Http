<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpExpectationFailed extends ResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::EXPECTATION_FAILED;
	}
}
