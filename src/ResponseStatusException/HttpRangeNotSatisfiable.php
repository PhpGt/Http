<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpRangeNotSatisfiable extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::REQUESTED_RANGE_NOT_SATISFIABLE;
	}
}