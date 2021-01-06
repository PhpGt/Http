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

	public function testWithValue() {
		$sut = new HeaderLine("some-key", "val1");
		$sut2 = $sut->withValue("val2", "val3", "val4");
		self::assertNotSame($sut, $sut2);
		self::assertEquals("val2,val3,val4", $sut2);
	}

	public function testWithAddedValue() {
		$sut = new HeaderLine("some-key", "val1");
		$sut2 = $sut->withAddedValue("val2", "val3", "val4");
		self::assertNotSame($sut, $sut2);
		self::assertEquals("val1,val2,val3,val4", $sut2);
	}

	public function testGetName() {
		$sut = new HeaderLine("Case-Sensitive", "val1");
		self::assertEquals("Case-Sensitive", $sut->getName());
	}

	public function testGetValue() {
		$sut = new HeaderLine("some-key", "val1", "val2", "val3");
		self::assertEquals("val1", $sut->getValue());
		self::assertEquals("val2", $sut->getValue(1));
		self::assertEquals("val3", $sut->getValue(2));
	}

	public function testGetValuesCommaSeparated() {
		$sut = new HeaderLine("some-key", "val1", "val2", "val3");
		self::assertEquals("val1,val2,val3", $sut->getValuesCommaSeparated());
	}

	public function testGetValuesNewlineSeparated() {
		$sut = new HeaderLine("some-key", "val1", "val2", "val3");
		self::assertEquals("val1\nval2\nval3", $sut->getValuesNewlineSeparated());
	}

	public function testIsNamed() {
		$sut = new HeaderLine("some-key");
		self::assertFalse($sut->isNamed("some-other-key"));
		self::assertTrue($sut->isNamed("some-key"));
		self::assertTrue($sut->isNamed("Some-Key"));
	}
}