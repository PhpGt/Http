<?php
namespace Gt\Http\Test;

use Gt\Http\Header\ResponseHeaders;
use Gt\Http\Response;
use Gt\Http\Uri;
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

	public function testRedirect_relative() {
		$requestUri = self::createMock(Uri::class);
		$requestUri->method("__toString")->willReturn("/example/start-path/");

		$sut = new Response(requestUri: $requestUri);
		$sut->redirect("../end-path/");
		$actualLocation = $sut->getHeaderLine("Location");

		self::assertSame("/example/end-path/", $actualLocation);
	}

	public function testReload_header() {
		$requestUri = self::createMock(Uri::class);
		$requestUri->method("__toString")->willReturn("/example/current-dir/");
		$sut = new Response(requestUri: $requestUri);
		self::assertEmpty($sut->getHeaderLine("Location"));
		$sut->reload();
		$actualLocation = $sut->getHeaderLine("Location");
		self::assertSame("/example/current-dir/", $actualLocation);
	}

	public function testReload_headerWithFullUri() {
		$requestUri = self::createMock(Uri::class);
		$requestUri->method("__toString")->willReturn("https://localhost:8080/example/current-dir/");
		$sut = new Response(requestUri: $requestUri);
		self::assertEmpty($sut->getHeaderLine("Location"));
		$sut->reload();
		$actualLocation = $sut->getHeaderLine("Location");
		self::assertSame("/example/current-dir/", $actualLocation);
	}

	public function testRedirect_crossDomain() {
		$requestUri = self::createMock(Uri::class);
		$requestUri->method("__toString")->willReturn("https://localhost:8080/example/current-dir/");
		$sut = new Response(requestUri: $requestUri);
		$sut->redirect("https://example.com");
		$actualLocation = $sut->getHeaderLine("Location");
		self::assertSame("https://example.com", $actualLocation);
	}

	public function testRedirect_toHttps() {
		$requestUri = self::createMock(Uri::class);
		$requestUri->method("__toString")->willReturn("http://example.com/");
		$sut = new Response(requestUri: $requestUri);
		$sut->redirect("https://example.com/");
		self::assertSame("https://example.com/", $sut->getHeaderLine("Location"));
	}
}
