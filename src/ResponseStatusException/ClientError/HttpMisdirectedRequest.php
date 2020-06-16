<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

class HttpMisdirectedRequest extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::MISDIRECTED_REQUEST;
	}
}