<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpRequestHeaderFieldsTooLarge extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::REQUEST_HEADER_FIELDS_TOO_LARGE;
	}
}