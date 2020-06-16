<?php
namespace Gt\Http\ResponseStatusException\ClientError;

use Gt\Http\StatusCode;

/**
 * The request entity has a media type which the server or resource does not
 * support. For example, the client uploads an image as image/svg+xml, but the
 * server requires that images use a different format.
 * @link https://httpstatuses.com/415
 */
class HttpUnsupportedMediaType extends AbstractClientErrorException {
	public function getHttpCode():int {
		return StatusCode::UNSUPPORTED_MEDIA_TYPE;
	}
}