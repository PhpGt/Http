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
		$mockResponseClass = new class extends Response {};
		/** @var MockObject|Request $request */
		$request = self::createMock(Request::class);
		$request->method("getHeaderLine")
			->with("accept")
			->willReturn("test/example");
		ResponseFactory::registerResponseClass(
			$mockResponseClass::class,
			"test/example"
		);

		self::assertInstanceOf(
			$mockResponseClass::class,
			ResponseFactory::create($request)
		);
	}

	public function testRegisterResponseClassDefault() {
		$mockResponseClass = new class extends Response {};

		/** @var MockObject|Request $request */
		$request = self::createMock(Request::class);
		$request->method("getHeaderLine")
			->with("accept")
			->willReturn(ResponseFactory::DEFAULT_ACCEPT);
		ResponseFactory::registerResponseClass(
			$mockResponseClass::class
		);

		self::assertInstanceOf(
			$mockResponseClass::class,
			ResponseFactory::create($request)
		);
	}
}
