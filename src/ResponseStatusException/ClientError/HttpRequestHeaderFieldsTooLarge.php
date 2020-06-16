<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

class HttpRequestHeaderFieldsTooLarge extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::REQUEST_HEADER_FIELDS_TOO_LARGE;
	}
}