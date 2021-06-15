<?php
namespace Gt\Http\Test;

use Gt\Cookie\CookieHandler;
use Gt\Http\InvalidRequestMethodHttpException;
use Gt\Http\RequestFactory;
use Gt\Http\RequestMethod;
use Gt\Http\ServerInfo;
use Gt\Http\ServerRequest;
use Gt\Http\Stream;
use Gt\Input\Input;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RequestFactoryTest extends TestCase {
	public function testCreateServerRequest() {
		$inputStream = self::createMock(Stream::class);

		/** @var MockObject|ServerInfo $serverInfo */
		$serverInfo = self::createMock(ServerInfo::class);
		$serverInfo->method("getServerProtocolVersion")
			->willReturn(123.000);
		$serverInfo->method("getRequestMethod")
			->willReturn(RequestMethod::METHOD_GET);
		/** @var MockObject|Input $input */
		$input = self::createMock(Input::class);
		$input->method("getStream")
			->willReturn($inputStream);
		/** @var MockObject|CookieHandler $cookieHandler */
		$cookieHandler = self::createMock(CookieHandler::class);

		$sut = new RequestFactory();
		$request = $sut->createServerRequest(
			$serverInfo,
			$input,
			$cookieHandler
		);

		self::assertInstanceOf(ServerRequest::class, $request);
		self::assertEquals("123", $request->getProtocolVersion());
	}

	public function testCreateServerRequestFromGlobals_noRequestMethod():void {
		$sut = new RequestFactory();
		self::expectException(InvalidRequestMethodHttpException::class);
		$sut->createServerRequestFromGlobalState([]);
	}

	public function testCreateServerRequestFromGlobals_invalidRequestMethod():void {
		$sut = new RequestFactory();
		self::expectException(InvalidRequestMethodHttpException::class);
		$sut->createServerRequestFromGlobalState([
			"REQUEST_METHOD" => "invalid"
		]);
	}

	public function testCreateServerRequestFromGlobals_checkRequestMethod():void {
		$sut = new RequestFactory();
		$request = $sut->createServerRequestFromGlobalState([
			"REQUEST_METHOD" => "POST"
		]);

		self::assertEquals("POST", $request->getMethod());
	}

	public function testCreateServerRequestFromGlobals_emptyUri():void {
		$sut = new RequestFactory();
		$request = $sut->createServerRequestFromGlobalState([
			"REQUEST_METHOD" => "POST"
		]);

		self::assertEquals("", $request->getUri()->getPath());
	}

	public function testCreateServerRequestFromGlobals_uri():void {
		$sut = new RequestFactory();
		$request = $sut->createServerRequestFromGlobalState([
			"REQUEST_METHOD" => "POST",
			"REQUEST_URI" => "/path/to/somewhere"
		]);

		self::assertEquals("/path/to/somewhere", $request->getUri()->getPath());
	}

	public function testCreateServerRequestFromGlobals_header():void {
		$sut = new RequestFactory();
		$request = $sut->createServerRequestFromGlobalState([
			"REQUEST_METHOD" => "GET",
			"HTTP_ACCEPT" => "*/*",
			"HTTP_HOST" => "PHPUnit"
		]);
		self::assertEquals("*/*", $request->getHeaderLine("Accept"));
		self::assertEquals("PHPUnit", $request->getHeaderLine("Host"));
	}

	public function testCreateServerRequestFromGlobals_postBody():void {
		$message = "Hello, PHPUnit!";
		$tmp = tempnam(sys_get_temp_dir(), "phpgt-");

		file_put_contents($tmp, $message);

		$sut = new RequestFactory();
		$request = $sut->createServerRequestFromGlobalState([
			"REQUEST_METHOD" => "POST",
		], $tmp);
		$body = $request->getBody();
		$content = $body->read(100);
		unlink($tmp);

		self::assertEquals($message, $content);
	}

	public function testCreateServerRequestFromGlobals_headers():void {
		$sut = new RequestFactory();
		$request = $sut->createServerRequestFromGlobalState([
			"REQUEST_METHOD" => "POST",
			"SERVER_PROTOCOL" => "HTTP/1.1"
		]);
		self::assertEquals("1.1", $request->getProtocolVersion());
	}
}
