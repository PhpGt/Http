<?php
namespace Gt\Http\Test\Header;

use Gt\Http\Header\HeaderLine;
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

	public function testAdd() {
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->add("Accept", "application/json");
		$headerArray = $headers->asArray();
		self::assertEquals("application/json", $headerArray["Accept"]);
	}

	public function testAddMultiple() {
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->add("Accept", "application/json", "application/xml");
		$headerArray = $headers->asArray();
		self::assertEquals("application/json,application/xml", $headerArray["Accept"]);

		$headers->add("Accept", "text/plain");
		$headerArray = $headers->asArray();
		self::assertEquals("application/json,application/xml,text/plain", $headerArray["Accept"]);
	}

	public function testAddMultipleCommaHeader() {
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->add(
			"Cookie-set",
			"language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com",
			"id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly"
		);
		$headerArray = $headers->asArray();
		$cookie = explode("\n", $headerArray["Cookie-set"]);
		self::assertContains("language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com", $cookie);
		self::assertContains("id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly", $cookie);
	}

	public function testSet() {
		$now = date("D, j M Y H:i:s T");
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->set("Date", $now);
		$headerArray = $headers->asArray();

		self::assertEquals($now, $headerArray["Date"]);
	}

	public function testRemove() {
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->remove("Date");
		$headerArray = $headers->asArray();
		self::assertArrayNotHasKey("Date", $headerArray);
	}

	public function testGetNotExist() {
		$headers = new Headers(self::HEADER_ARRAY);
		$h = $headers->get("X-NOT-EXISTS");
		self::assertNull($h);
	}

	public function testGet() {
		$headers = new Headers(self::HEADER_ARRAY);
		$h = $headers->get("Date");
		self::assertEquals(self::HEADER_ARRAY["Date"], $h);
	}

	public function testGetMultiple() {
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->add("Accept", "application/json", "application/xml");
		self::assertEquals("application/json,application/xml", $headers->get("Accept"));
	}

	public function testGetMultipleCommas() {
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->add(
			"Cookie-set",
			"language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com",
			"id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly"
		);
		self::assertEquals(
			"language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com"
			. "\n"
			. "id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly",
			$headers->get("Cookie-set")
		);
	}

	public function testGetAllNotExist() {
		$headers = new Headers(self::HEADER_ARRAY);
		$all = $headers->getAll("X-not-exist");
		self::assertEmpty($all);
	}

	public function testGetAll() {
		$headerValues = ["application/json", "application/xml"];
		$headers = new Headers(self::HEADER_ARRAY);
		$headers->add("Accept", ...$headerValues);
		$all = $headers->getAll("aCcEpT");

		foreach($all as $i => $value) {
			self::assertEquals($headerValues[$i], $value);
		}
	}
}