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

	public function testGetCookieParams() {
		$cookieParams = [
			"COOKIE1" => "VALUE1",
			"COOKIE2" => "VALUE2",
		];
		$sut = self::getServerRequest(
			null,
			null,
			[],
			[],
			[],
			$cookieParams
		);
		self::assertEquals($cookieParams, $sut->getCookieParams());
	}

	public function testWithCookieParams() {
		$cookieParams1 = [
			"COOKIE1" => "VALUE1",
			"COOKIE2" => "VALUE2",
		];
		$cookieParams2 = [
			"COOKIE3" => "VALUE3",
			"COOKIE4" => "VALUE4",
		];
		$sut1 = self::getServerRequest(
			null,
			null,
			[],
			[],
			[],
			$cookieParams1
		);
		$sut2 = $sut1->withCookieParams($cookieParams2);
		self::assertEquals($cookieParams1, $sut1->getCookieParams());
		self::assertEquals($cookieParams2, $sut2->getCookieParams());
	}

	public function testGetQueryParams() {
		$params = [
			"key1" => "value1",
			"key2" => "value2",
		];
		$server = [
			"QUERY_STRING" => http_build_query($params),
		];
		$sut = self::getServerRequest(
			null,
			null,
			[],
			$server
		);
		self::assertEquals($params, $sut->getQueryParams());
	}

// TODO: Work out problem when using cloned mock in withQueryParams below.
	public function testWithQueryParams() {
		$params1 = [
			"key1" => "value1",
			"key2" => "value2",
		];
		$params2 = [
			"key3" => "value3",
			"key4" => "value4",
		];
		$server1 = [
			"QUERY_STRING" => http_build_query($params1),
		];
		$sut1 = self::getServerRequest(
			null,
			null,
			[],
			$server1
		);
		$sut2 = $sut1->withQueryParams($params2);
		self::assertEquals($params1, $sut1->getQueryParams());
		self::assertEquals($params2, $sut2->getQueryParams());
	}

	public function testGetUploadedFilesEmpty() {
		$sut = self::getServerRequest();
		self::assertEmpty($sut->getUploadedFiles());
	}

	public function testGetUploadedFiles() {
		$sut = self::getServerRequest(
			"post",
			"/example",
			[],
			[],
			[
				"files" => [

				]
			]
		);
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
		$mock->method("getQueryParams")
			->willReturnCallback(function()use($server) {
				parse_str($server["QUERY_STRING"], $result);
				return $result;
			});
		$mock->method("withQueryParams")
			->willReturnCallback(function($params)use($mock) {
				$clone = self::createMock(ServerInfo::class);
				$clone->method("getQueryParams")
					->willReturn($params);
				return $clone;
			});
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
		$mock->method("asArray")
			->willReturn($cookies);
		return $mock;
	}
}