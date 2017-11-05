<?php
namespace Gt\Http;

class RequestFactory {
	public static function create(array $server = null):Request {
		if(is_null($server)) {
			$server = $_SERVER;
		}

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