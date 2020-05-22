<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * The request was well-formed but was unable to be followed due to semantic
 * errors.
 * @link https://httpstatuses.com/422
 */
class HttpUnprocessableEntity extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::UNPROCESSABLE_ENTITY;
	}
}