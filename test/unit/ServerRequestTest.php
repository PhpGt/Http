<?php
namespace Gt\Http\Test;

use Gt\Cookie\Cookie;
use Gt\Cookie\CookieHandler;
use Gt\Http\Header\RequestHeaders;
use Gt\Http\ServerInfo;
use Gt\Http\ServerRequest;
use Gt\Http\Uri;
use Gt\Input\Input;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase {
	public function testGetServerParams() {
		$serverParams = [
			"KEY1" => "VALUE1",
			"KEY2" => "VALUE2",
		];
		$sut = self::getServerRequest(
			null,
			null,
			[],
			$serverParams
		);
		self::assertEquals($serverParams, $sut->getServerParams());
	}

	protected function getServerRequest(
		string $method = null,
		string $uri = null,
		array $headerArray = [],
		array $serverArray = [],
		array $inputArray = [],
		array $cookieArray = []
	):ServerRequest {
		$method = $method ?? "GET";
		$uri = self::getMockUri($uri ?? "/");
		$headers = self::getMockHeaders($headerArray);
		$serverInfo = self::getMockServerInfo($serverArray);
		$input = self::getMockInput($inputArray);
		$cookieHandler = self::getMockCookieHandler($cookieArray);

		$sut = new ServerRequest(
			$method,
			$uri,
			$headers,
			$serverInfo,
			$input,
			$cookieHandler
		);

		return $sut;
	}

	/** @return MockObject|Uri */
	protected function getMockUri(string $uriPath):MockObject {
		$mock = self::createMock(Uri::class);
		return $mock;
	}

	/** @return MockObject|RequestHeaders */
	protected function getMockHeaders(array $headers = []):MockObject {
		$mock = self::createMock(RequestHeaders::class);
		return $mock;
	}

	/** @return MockObject|ServerInfo */
	protected function getMockServerInfo(array $server = []):MockObject {
		$mock = self::createMock(ServerInfo::class);
		$mock->method("getParams")
			->willReturn($server);
		return $mock;
	}

	/** @return MockObject|Input */
	protected function getMockInput(array $input = []):MockObject {
		$mock = self::createMock(Input::class);
		return $mock;
	}

	/** @return MockObject|CookieHandler */
	protected function getMockCookieHandler(array $cookies = []):MockObject {
		$mock = self::createMock(CookieHandler::class);
		return $mock;
	}
}