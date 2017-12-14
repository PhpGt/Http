<?php
namespace Gt\Http\Test;

use PHPUnit\Framework\TestCase;

class TestTest extends TestCase {
	public function testFalseIsNotTrue() {
		self::assertNotTrue(false);
	}
}