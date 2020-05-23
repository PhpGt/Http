<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpMisdirectedRequest extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::MISDIRECTED_REQUEST;
	}
}