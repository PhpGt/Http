<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpLengthRequired extends ResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::LENGTH_REQUIRED;
	}
}
