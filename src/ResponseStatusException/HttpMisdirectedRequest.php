<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpMisdirectedRequest extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::MISDIRECTED_REQUEST;
	}
}