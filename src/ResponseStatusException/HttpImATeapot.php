<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpImATeapot extends AbstractResponseStatusException {
	public function getHttpCode():int {
		return StatusCode::IM_A_TEAPOT;
	}
}