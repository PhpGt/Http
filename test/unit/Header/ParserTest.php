<?php
namespace Gt\Http\Test\Header;

use Gt\Http\Header\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {
	const HEADERS_BASIC_REQUEST = <<<HEADERS
GET /example/picture.jpg HTTP/1.1
HOST: example.com
User-Agent: PHPUnit Test (PHP.Gt)
HEADERS;

	const HEADERS_BASIC_RESPONSE = <<<HEADERS
HTTP/1.1 200 OK
Date: Thu, 1 Jan 1970 00:00:00 UTC
Server: PHPUnit
X-Test-For: PHP.Gt
HEADERS;

	public function testGetProtocolVersionRequest() {
		$parser = new Parser(self::HEADERS_BASIC_REQUEST);
		self::assertEquals(
			"1.1",
			$parser->getProtocolVersion()
		);
	}

	public function testGetProtocolVersion2Request() {
		$headers = str_replace(
			"HTTP/1.1",
			"HTTP/2",
			self::HEADERS_BASIC_REQUEST
		);
		$parser = new Parser($headers);
		self::assertEquals("2", $parser->getProtocolVersion());
	}

	public function testGetProtocolVersionResponse() {
		$parser = new Parser(self::HEADERS_BASIC_RESPONSE);
		self::assertEquals(
			"1.1",
			$parser->getProtocolVersion()
		);
	}

	public function testGetResponseCode() {
		$parser = new Parser(self::HEADERS_BASIC_RESPONSE);
		self::assertEquals(
			200,
			$parser->getStatusCode()
		);
	}
}
