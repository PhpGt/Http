<?php
namespace Gt\Http\Test;

use Gt\Http\Header\ResponseHeaders;
use Gt\Http\Response;
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
}
