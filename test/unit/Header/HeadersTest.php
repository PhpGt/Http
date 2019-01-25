<?php
namespace Gt\Http\Test\Header;

use Gt\Http\Header\Headers;
use PHPUnit\Framework\TestCase;

class HeadersTest extends TestCase {
	const HEADER_ARRAY = [
		"Date" => "Thu, 1 Jan 1970 00:00:00 UTC",
		"Etag" => "0000aaaabbbbcccceeeeffff",
		"Content-Type" => "text/plain; charset=UTF-8",
	];

	public function testAsArray() {
		$headers = new Headers(self::HEADER_ARRAY);
		self::assertEquals(
			self::HEADER_ARRAY,
			$headers->asArray()
		);
	}
}