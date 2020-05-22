<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpGone extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::GONE;
	}
}