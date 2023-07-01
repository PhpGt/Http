<?php
namespace Gt\Http\Test;

use Gt\Http\Header\ResponseHeaders;
use Gt\Http\Request;
use Gt\Http\Response;
use Gt\Http\StatusCode;
use Gt\Http\Stream;
use Gt\Http\Uri;
use Gt\Json\JsonObject;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase {
	public function testGetStatusCode() {
		$sut = new Response(123);
		self::assertEquals(123, $sut->getStatusCode());
	}

	public function testWithStatusCode() {
		$sut = new Response(123);
		self::assertEquals(
			321,
			$sut->withStatus(321)->getStatusCode()
		);
	}

	public function testGetReasonPhraseDefault() {
		$notFound = new Response(404);
		self::assertEquals("Not Found", $notFound->getReasonPhrase());

		$noContent = new Response(204);
		self::assertEquals("No Content", $noContent->getReasonPhrase());

		$teapot = new Response(418);
		self::assertEquals("I'm a teapot", $teapot->getReasonPhrase());
	}

	public function testGetResponseHeadersDefault() {
		$sut = new Response(123);
		$headers = $sut->getResponseHeaders();
		self::assertInstanceOf(ResponseHeaders::class, $headers);
		self::assertCount(0, $headers);
	}

	public function testRedirect() {
		$callbackCount = 0;
		$callback = function()use(&$callbackCount) {
			$callbackCount++;
		};

		$sut = new Response(200);
		$sut->setExitCallback($callback);

		self::assertSame(0, $callbackCount);
		$sut->redirect("/somewhere/");
		self::assertSame(1, $callbackCount);

		self::assertSame(
			"/somewhere/",
			$sut->getHeaderLine("Location")
		);
	}

	public function testReloadKeepsQuery() {
		$expectedRelativePath = "./?test=123";

		$uri = self::createMock(Uri::class);
		$uri->expects(self::never())
			->method("withQuery");
		$uri->method("__toString")
			->willReturn($expectedRelativePath);

		$request = self::createMock(Request::class);
		$request->method("getUri")
			->willReturn($uri);

		$sut = new Response(200, request: $request);
		self::assertSame(StatusCode::OK, $sut->getStatusCode());
		$sut->reload();
		self::assertSame(StatusCode::SEE_OTHER, $sut->getStatusCode());
		self::assertSame($expectedRelativePath, $sut->getHeaderLine("Location"));
	}

	public function testReloadWithoutQuery() {
		$expectedRelativePath = "./";

		$uri = self::createMock(Uri::class);
		$uri->expects(self::once())
			->method("withQuery")
			->with("")
			->willReturn($uri);
		$uri->method("__toString")
			->willReturn($expectedRelativePath);

		$request = self::createMock(Request::class);
		$request->method("getUri")
			->willReturn($uri);

		$sut = new Response(200, request: $request);
		$sut->reloadWithoutQuery();
		self::assertSame(StatusCode::SEE_OTHER, $sut->getStatusCode());
		self::assertSame($expectedRelativePath, $sut->getHeaderLine("Location"));
	}

	public function testJson():void {
		$jsonString = json_encode(["name" => "phpgt"]);

		$actualJson = null;

		$stream = new Stream();
		$stream->write($jsonString);

		$sut = (new Response())->withBody($stream);

		$sut->json()->then(function(JsonObject $json) use(&$actualJson) {
			$actualJson = $json;
		});

		self::assertSame("phpgt", $actualJson->getString("name"));
	}
}
