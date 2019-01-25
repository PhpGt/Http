<?php
namespace Gt\Http\Test;

use Gt\Http\Response;
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

	public function testWithStatusCodeSame() {
		$sut = new Response(123);
		$sut2 = $sut->withStatus(123);
		self::assertSame($sut, $sut2);
	}
}