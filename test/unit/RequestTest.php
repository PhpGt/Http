<?php
namespace Gt\Http\Test;

use Gt\Http\Header\RequestHeaders;
use Gt\Http\Request;
use Gt\Http\Uri;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {
	public function testGetRequestTarget() {
		$uriPath = "/test?key1=val1&key2=val2";
		$sut = new Request(
			"get",
			self::getUriMock($uriPath),
			self::getHeadersMock()
		);
		self::assertEquals($uriPath, $sut->getRequestTarget());
	}

	/** @return MockObject|Uri */
	protected function getUriMock(string $uriPath):MockObject {
		$partPath = parse_url($uriPath, PHP_URL_PATH);
		$partQuery = parse_url($uriPath, PHP_URL_QUERY);
		$uri = self::createMock(Uri::class);
		$uri->method("getPath")
			->willReturn($partPath);
		$uri->method("getQuery")
			->willReturn($partQuery);
		return $uri;
	}

	/** @return MockObject|RequestHeaders */
	protected function getHeadersMock():MockObject {
		$headers = self::createMock(RequestHeaders::class);
		return $headers;
	}
}