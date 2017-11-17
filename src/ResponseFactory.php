<?php
namespace Gt\Http;

use Psr\Http\Message\RequestInterface;

class ResponseFactory {
	/**
	 * A Response object is a PSR-7 compatible object that is created here from the current
	 * Request. The type of Response that is returned is determined by the type of request.
	 * @see http://www.php-fig.org/psr/psr-7
	 */
	public static function create(RequestInterface $request):Response {
		return new Response();
	}
}