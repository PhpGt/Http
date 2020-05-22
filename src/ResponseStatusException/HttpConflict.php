<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpConflict extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::CONFLICT;
	}
}