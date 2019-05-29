<?php
namespace Gt\Http\Test;

use Gt\Http\Header\Headers;
use Gt\Http\Header\RequestHeaders;
use Gt\Http\Message;
use Gt\Http\Request;
use Gt\Http\RequestMethod;
use Gt\Http\Uri;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase {
	/** @dataProvider data_request */
	public function testGetProtocolVersionRequest(
		string $method,
		string $uriString,
		array $headersArray
	) {
		/** @var MockObject|Uri $uri */
		$uri = self::createMock(Uri::class);
		$uri->method("__toString")
			->willReturn($uriString);

		/** @var MockObject|RequestHeaders $headers */
		$headers = self::createMock(RequestHeaders::class);
		$headers->method("getFirst")
			->willReturn($headersArray[0]);

		$request = new Request(
			$method,
			$uri,
			$headers
		);

		$expected = substr(
			$headersArray[0],
			strpos($headersArray[0], "/") + 1
		);
		$expected = substr(
			$expected,
			0,
			strpos($expected, " ")
		);

		$protocolVersion = $request->getProtocolVersion();
		self::assertEquals($expected, $protocolVersion);
	}

	public function data_request():array {
		$data = [];

		for($i = 0; $i < 10; $i++) {
			$method = RequestMethod::ALLOWED_METHODS[
				array_rand(RequestMethod::ALLOWED_METHODS)
			];
			$uriString = self::generateUri();
			$headersArray = self::generateHeaders();

			$data []= [
				$method,
				$uriString,
				$headersArray,
			];
		}

		return $data;
	}

	public static function generateUri():string {
		$uri = "http";
		if(rand(0, 1)) {
			$uri .= "s";
		}
		$uri .= "://";

		$uri .= uniqid("www.");
		$uri .= ".com";

		if(rand(0, 1)) {
			$pathParts = rand(1, 10);
			for($i = 0; $i < $pathParts; $i++) {
				$uri .= "/";
				$uri .= uniqid();
			}
		}

		if(rand(0, 1)) {
			$uri .= "?";
			$qsParts = rand(1, 10);
			for($i = 0; $i < $qsParts; $i++) {
				if($i > 0) {
					$uri .= "&";
				}

				$qsParts .= uniqid("key-");
				$qsParts .= "=";
				$qsParts .= uniqid("value-");
			}
		}

		return $uri;
	}

	public static function generateHeaders():array {
		$headers = [];

		$firstLine = "";
		if(rand(0, 1)) {
			$firstLine .= strtoupper(
				substr(uniqid(), 0, 4)
			);
			$firstLine .= "/";
			$firstLine .= rand(1, 9);
		}
		else {
			$firstLine .= "HTTP";
			$firstLine .= "/";
			if(rand(0, 1)) {
				$firstLine .= "1.1";
			}
			else {
				$firstLine .= rand(2, 3);
			}
		}

		$firstLine .= " ";
		$firstLine .= rand(200, 550);
		$firstLine .= " ";
		$firstLine .= "TEST";
		$headers []= $firstLine;

		for($i = 0; $i < rand(1, 10); $i++) {
			$line = uniqid("Key-");
			$line .= ": ";
			$line .= uniqid("value-");
			$headers []= $line;
		}

		return $headers;
	}
}