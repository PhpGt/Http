<?php
namespace Gt\Http;

use Psr\Http\Message\StreamInterface;

class RequestFactory {
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

		foreach($headers as $header) {
			$request = $request->withAddedHeader(
				$header->getName(),
				$header->getValues()
			);
		}

		return $request;
	}
}