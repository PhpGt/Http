<?php
namespace Gt\Http;

use Gt\Input\Input;
use Gt\Http\Header\RequestHeaders;
use Psr\Http\Message\ServerRequestInterface;

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
		Input $input
	):ServerRequestInterface {
		$uri = new Uri($serverInfo->getRequestUri());
		$headers = new RequestHeaders($serverInfo->getHttpHeadersArray());

		return (new ServerRequest(
			$serverInfo->getRequestMethod(),
			$uri,
			$headers,
			$serverInfo->getParams()
		))
		->withProtocolVersion((string)$serverInfo->getServerProtocolVersion())
		->withBody($input->getStream());
	}

	/**
	 * A Request object is a PSR-7 compatible object that is created here
	 * from the current global state (as passed in via the appropriate
	 * associative arrays).
	 * @param array<string, string> $server
	 * @param array<string, array<string, string>> $files
	 * @param array<string, string> $get
	 * @param array<string, string> $post
	 *
	 * @link http://www.php-fig.org/psr/psr-7/
	 */
	public function createServerRequestFromGlobalState(
		array $server,
		array $files,
		array $get,
		array $post,
		string $inputPath = "php://input"
	):ServerRequestInterface {
		$method = $server["REQUEST_METHOD"] ?? "";
		$uri = new Uri($server["REQUEST_URI"] ?? null);
		$headers = new RequestHeaders();

		if($secure = $server["HTTPS"] ?? false) {
			$uri = $uri->withScheme("https");
		}
		else {
			$uri = $uri->withScheme("http");
		}

		if($port = $server["SERVER_PORT"]) {
			$uri = $uri->withPort($port);
		}

		if($host = $server["HTTP_HOST"]) {
			$host = strtok($host, ":");
			$uri = $uri->withHost($host);
		}

		if($query = $server["QUERY_STRING"]) {
			$uri = $uri->withQuery($query);
		}

		foreach($server as $key => $value) {
			if(str_starts_with($key, "HTTP_")) {
				$headerKey = substr($key, strlen("HTTP_"));
				$headers->add($headerKey, $value);
			}
		}

		$request = new ServerRequest(
			$method,
			$uri,
			$headers,
			$server,
			$files,
			$get,
			$post
		);
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
