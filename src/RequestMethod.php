<?php
namespace Gt\Http;

class RequestMethod {
	const METHOD_GET = "GET";
	const METHOD_HEAD = "HEAD";
	const METHOD_POST = "POST";
	const METHOD_PUT = "PUT";
	const METHOD_OPTIONS = "OPTIONS";
	const METHOD_DELETE = "DELETE";
	const METHOD_TRACE = "TRACE";

	const ALLOWED_METHODS = [
		self::METHOD_GET,
		self::METHOD_HEAD,
		self::METHOD_POST,
		self::METHOD_PUT,
		self::METHOD_OPTIONS,
		self::METHOD_DELETE,
		self::METHOD_TRACE,
	];

	public static function filterMethodName(string $name):string {
		$name = strtoupper($name);

		if(!in_array($name, self::ALLOWED_METHODS)) {
			throw new InvalidRequestMethodHttpException($name);
		}

		return $name;
	}
}