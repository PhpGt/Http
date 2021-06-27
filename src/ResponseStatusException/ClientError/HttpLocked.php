<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpLocked extends ResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::LOCKED;
	}
}
