<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

class HttpRangeNotSatisfiable extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::REQUESTED_RANGE_NOT_SATISFIABLE;
	}
}