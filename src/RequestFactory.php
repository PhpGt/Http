<?php
namespace Gt\Http;

use Gt\Cookie\CookieHandler;
use Gt\Input\Input;
use Gt\Http\Header\RequestHeaders;

class RequestFactory {
	/**
	 * A Request object is a PSR-7 compatible object that is created here
	 * from the current ServerInfo (containing request headers, URI,
	 * protocol information, etc.) and the input body (post fields or other
	 * incoming data).
	 * @link http://www.php-fig.org/psr/psr-7/
	 */
	public function createServerRequest(
		ServerInfo $serverInfo,
		Input $input,
		CookieHandler $cookieHandler
	):Request {
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
		->withProtocolVersion((string)$serverInfo->getServerProtocolVersion())
		->withBody($input->getStream());

		return $request;
	}

	/**
	 * A Request object is a PSR-7 compatible object that is created here
	 * from the current global state (as passed in via the appropriate
	 * associative arrays).
	 * @param array<string, string> $server
	 *
	 * @link http://www.php-fig.org/psr/psr-7/
	 */
	public function createServerRequestFromGlobalState(
		array $server,
		string $inputPath = "php://input"
	):Request {
		$method = $server["REQUEST_METHOD"] ?? "";
		$uri = new Uri($server["REQUEST_URI"] ?? null);
		$headers = new RequestHeaders();

		foreach($server as $key => $value) {
			if(str_starts_with($key, "HTTP_")) {
				$headerKey = substr($key, strlen("HTTP_"));
				$headers->add($headerKey, $value);
			}
		}

		$request = new Request($method, $uri, $headers);
		$stream = new Stream($inputPath);
		$request = $request->withBody($stream);

		if($protocolString = $server["SERVER_PROTOCOL"] ?? null) {
			if($slashPos = strpos($protocolString, "/")) {
				$protocolString = substr($protocolString, $slashPos + 1);
			}

			$request = $request->withProtocolVersion($protocolString);
		}

		return $request;
	}
}
