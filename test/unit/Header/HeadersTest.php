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

	public function testFromArray() {
		$headers = new Headers(self::HEADER_ARRAY);
		$newHeaders = [
			"X-New" => "Example new header",
			"X-Expected" => "These headers should also be found",
		];

		$headers->fromArray($newHeaders);

		$combined = array_merge(self::HEADER_ARRAY, $newHeaders);
		self::assertEquals($combined, $headers->asArray());
	}

	public function testContains() {
		$headers = new Headers(self::HEADER_ARRAY);
		self::assertTrue($headers->contains("Etag"));
		self::assertFalse($headers->contains("Ftag"));
	}
}