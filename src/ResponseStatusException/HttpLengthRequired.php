<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpLengthRequired extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::LENGTH_REQUIRED;
	}
}