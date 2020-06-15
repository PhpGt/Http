<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpConflict extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::CONFLICT;
	}
}