<?php
namespace Gt\Http;

use Gt\Cookie\CookieHandler;
use Gt\Input\Input;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Gt\Http\Header\RequestHeaders;

class RequestFactory {
	/**
	 * A Request object is a PSR-7 compatible object that is created here from the current
	 * ServerInfo (containing request headers, URI, protocol information, etc.) and the input
	 * body (post fields or other incoming data).
	 * @see http://www.php-fig.org/psr/psr-7/
	 */
	public static function createServerRequest(
		ServerInfo $serverInfo,
		Input $input,
		CookieHandler $cookieHandler
	):ServerRequestInterface {
		$uri = new Uri($serverInfo->getRequestUri());
		$headers = new RequestHeaders($serverInfo->getHttpHeadersArray());

		$request = (new ServerRequest(
			$serverInfo->getRequestMethod(),
			$uri,
			$headers,
			$serverInfo,
			$input,
			$cookieHandler
		))
		->withProtocolVersion($serverInfo->getServerProtocolVersion())
		->withBody($input->getStream());

		return $request;
	}
}