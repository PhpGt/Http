<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpLocked extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::LOCKED;
	}
}