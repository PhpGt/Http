<?php
namespace Gt\Http;

class RequestFactory {
	public static function create(array $server):Request {
		$serverInfo = new ServerInfo($server);
		$uri = new Uri($serverInfo->getRequestUri());
		$headers = new RequestHeaders($serverInfo->getHttpHeadersArray());
		return new Request(
			$serverInfo->getRequestMethod(),
			$uri,
			$headers
		);
	}
}