<?php
namespace Gt\Http\Test\Header;

use Gt\Http\Header\HeaderLine;
use PHPUnit\Framework\TestCase;

class HeaderLineTest extends TestCase {
	public function testToString() {
		$sut = new HeaderLine("some-key", "some-value");
		self::assertEquals("some-value", $sut);
	}

	public function testToStringMultiple() {
		$sut = new HeaderLine("some-key", "val1", "val2", "val3");
		self::assertEquals("val1,val2,val3", $sut);
	}
}