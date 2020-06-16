<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

/**
 * The request was well-formed but was unable to be followed due to semantic
 * errors.
 * @link https://httpstatuses.com/422
 */
class HttpUnprocessableEntity extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::UNPROCESSABLE_ENTITY;
	}
}