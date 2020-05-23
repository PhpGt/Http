<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * Transparent content negotiation for the request results in a circular
 * reference.
 * @link https://httpstatuses.com/506
 */
class HttpVariantAlsoNegotiates extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::VARIANT_ALSO_NEGOTIATES;
	}
}