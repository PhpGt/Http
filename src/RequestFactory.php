<?php
namespace Gt\Http;

use Psr\Http\Message\StreamInterface;
use Gt\Http\Header\RequestHeaders;

class RequestFactory {
	/**
	 * A Request object is a PSR-7 compatible object that is created here from the current
	 * ServerInfo (containing request headers, URI, protocol information, etc.) and the input
	 * body (post fields or other incoming data).
	 * @see http://www.php-fig.org/psr/psr-7/
	 */
	public static function create(ServerInfo $serverInfo, StreamInterface $body):Request {
		$uri = new Uri($serverInfo->getRequestUri());
		$headers = new RequestHeaders($serverInfo->getHttpHeadersArray());
		$request = (new Request(
			$serverInfo->getRequestMethod(),
			$uri,
			$headers
		))
		->withProtocolVersion($serverInfo->getServerProtocolVersion())
		->withBody($body);

		return $request;
	}
}