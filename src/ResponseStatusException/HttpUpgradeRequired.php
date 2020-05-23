<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\StatusCode;

/**
 * The client should switch to a different protocol such as TLS/1.0, given in
 * the Upgrade header field.
 * @link https://httpstatuses.com/426
 */
class HttpUpgradeRequired extends AbstractResponseStatusException {
	protected function getHttpCode():int {
		return StatusCode::UPGRADE_REQUIRED;
	}
}