<?php
namespace Gt\Http\Test;

use Gt\Cookie\CookieHandler;
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

		$request = RequestFactory::createServerRequest(
			$serverInfo,
			$input,
			$cookieHandler
		);

		self::assertInstanceOf(ServerRequest::class, $request);
		self::assertEquals("123", $request->getProtocolVersion());
	}
}