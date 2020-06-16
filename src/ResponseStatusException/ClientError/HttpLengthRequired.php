<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpLengthRequired extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::LENGTH_REQUIRED;
	}
}