<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpRequestHeaderFieldsTooLarge extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::REQUEST_HEADER_FIELDS_TOO_LARGE;
	}
}