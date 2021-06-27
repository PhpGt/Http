<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpGone extends ResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::GONE;
	}
}
