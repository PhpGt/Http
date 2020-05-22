<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

class HttpImATeapot extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::IM_A_TEAPOT;
	}
}