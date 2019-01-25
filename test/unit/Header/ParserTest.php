<?php
namespace Gt\Http\Test\Header;

use Gt\Http\Header\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {
	const HEADERS_BASIC = <<<HEADERS
GET /example/picture.jpg HTTP/1.1
HOST: example.com
User-Agent: PHPUnit Test (PHP.Gt)
HEADERS;

	public function testGetProtocolVersion() {
		$parser = new Parser(self::HEADERS_BASIC);
		self::assertEquals(
			"1.1",
			$parser->getProtocolVersion()
		);
	}

	public function testGetProtocolVersion2() {
		$headers = str_replace(
			"HTTP/1.1",
			"HTTP/2",
			self::HEADERS_BASIC
		);
		$parser = new Parser($headers);
		self::assertEquals("2", $parser->getProtocolVersion());
	}
}
