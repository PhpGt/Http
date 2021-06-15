<?php
namespace Gt\Http\Test;

use Gt\Http\Request;
use Gt\Http\Response;
use Gt\Http\ResponseFactory;
use Gt\Http\UnknownAcceptHeaderException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResponseFactoryTest extends TestCase {
	public function testCreateNoHeaderString() {
		self::expectException(UnknownAcceptHeaderException::class);
		/** @var MockObject|Request $request */
		$request = self::createMock(Request::class);
		ResponseFactory::create($request);
	}

	public function testCreateUnknownAcceptHeader() {
		self::expectException(UnknownAcceptHeaderException::class);
		/** @var MockObject|Request $request */
		$request = self::createMock(Request::class);
		$request->method("getHeaderLine")
			->with("accept")
			->willReturn("text/html");
		ResponseFactory::create($request);
	}

	public function testCreateAfterRegisteringResponseClass() {
		$mockResponseClass = self::getMockClass(Response::class);
		/** @var MockObject|Request $request */
		$request = self::createMock(Request::class);
		$request->method("getHeaderLine")
			->with("accept")
			->willReturn("test/example");
		ResponseFactory::registerResponseClass(
			$mockResponseClass,
			"test/example"
		);

		self::assertInstanceOf(
			$mockResponseClass,
			ResponseFactory::create($request)
		);
	}

	public function testRegisterResponseClassDefault() {
		$mockResponseClass = self::getMockClass(Response::class);
		/** @var MockObject|Request $request */
		$request = self::createMock(Request::class);
		$request->method("getHeaderLine")
			->with("accept")
			->willReturn(ResponseFactory::DEFAULT_ACCEPT);
		ResponseFactory::registerResponseClass(
			$mockResponseClass
		);

		self::assertInstanceOf(
			$mockResponseClass,
			ResponseFactory::create($request)
		);
	}
}
