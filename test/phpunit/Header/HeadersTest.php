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

	public function testContains() {
		$headers = new Headers(self::HEADER_ARRAY);
		self::assertTrue($headers->contains("Etag"));
		self::assertFalse($headers->contains("Ftag"));
	}

	public function testWith() {
		$sut = new Headers(self::HEADER_ARRAY);
		$headerArray = $sut->asArray();
		self::assertArrayNotHasKey("Accept", $headerArray);

		$sut2 = $sut->withHeader("Accept", "application/json");
		$headerArray = $sut2->asArray();

		foreach(self::HEADER_ARRAY as $key => $value) {
			self::assertEquals($value, $headerArray[$key]);
		}
		self::assertEquals("application/json", $headerArray["Accept"]);
	}

	public function testWithSecondCallOverwrites() {
		$sut = new Headers(self::HEADER_ARRAY);
		$sut = $sut->withHeader("Accept", "application/json", "application/xml");
		$headerArray = $sut->asArray();
		self::assertEquals("application/json,application/xml", $headerArray["Accept"]);

		$sut = $sut->withHeader("Accept", "text/plain");
		$headerArray = $sut->asArray();
		self::assertEquals("text/plain", $headerArray["Accept"]);
	}

	public function testWithAddedValue() {
		$sut = new Headers(self::HEADER_ARRAY);
		$sut = $sut->withHeader("Accept", "application/json", "application/xml");
		$sut = $sut->withAddedHeaderValue("Accept", "text/plain");
		$headerArray = $sut->asArray();
		self::assertEquals("application/json,application/xml,text/plain", $headerArray["Accept"]);
	}

	public function testWithAddedValueWhenNoKeyExists() {
		$sut = new Headers();
		$sut = $sut->withAddedHeaderValue("Accept", "text/plain");
		$headerArray = $sut->asArray();
		self::assertEquals("text/plain", $headerArray["Accept"]);
	}

	public function testAddMultipleCommaHeader() {
		$sut = new Headers(self::HEADER_ARRAY);
		$sut = $sut->withAddedHeaderValue(
			"Cookie-set",
			"language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com",
			"id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly"
		);
		$headerArray = $sut->asArray();
		$cookie = explode("\n", $headerArray["Cookie-set"]);
		self::assertContains("language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com", $cookie);
		self::assertContains("id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly", $cookie);
	}

	public function testWithoutHeader() {
		$sut = new Headers(self::HEADER_ARRAY);
		$sut = $sut->withoutHeader("Date");
		$headerArray = $sut->asArray();
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
		$sut = new Headers(self::HEADER_ARRAY);
		$sut = $sut->withHeader("Accept", "application/json", "application/xml");
		self::assertEquals("application/json,application/xml", $sut->get("Accept"));
	}

	public function testGetMultipleCommas() {
		$sut = new Headers(self::HEADER_ARRAY);
		$sut = $sut->withHeader(
			"Cookie-set",
			"language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com",
			"id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly"
		);
		self::assertEquals(
			"language=en; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com"
			. "\n"
			. "id=123; expires=Thu, 1-Jan-1970 00:00:00 UTC; path=/; domain=example.com httponly",
			$sut->get("Cookie-set")
		);
	}

	public function testGetAllNotExist() {
		$headers = new Headers(self::HEADER_ARRAY);
		$all = $headers->getAll("X-not-exist");
		self::assertEmpty($all);
	}

	public function testGetAll() {
		$headerValues = ["application/json", "application/xml"];
		$sut = new Headers(self::HEADER_ARRAY);
		$sut = $sut->withHeader("Accept", ...$headerValues);
		$all = $sut->getAll("aCcEpT");

		foreach($all as $i => $value) {
			self::assertEquals($headerValues[$i], $value);
		}
	}

	public function testIterator() {
		$headers = new Headers(self::HEADER_ARRAY);
		$kvp = [];
		foreach($headers as $i => $headerLine) {
			$kvp[$headerLine->getName()] = $headerLine;
		}

		self::assertCount(count(self::HEADER_ARRAY), $kvp);

		foreach(self::HEADER_ARRAY as $expectedKey => $expectedValue) {
			self::assertArrayHasKey($expectedKey, $kvp);
			self::assertEquals($expectedValue, $kvp[$expectedKey]);
		}
	}

	public function testCaseInsensitive() {
		$headers = new Headers(self::HEADER_ARRAY);
		self::assertTrue($headers->contains("date"));
		self::assertTrue($headers->contains("Date"));
		self::assertTrue($headers->contains("DATE"));

		self::assertEquals(
			self::HEADER_ARRAY["Date"],
			$headers->get("dAtE")
		);

		self::assertTrue($headers->contains("ConTent-Type"));
	}
}