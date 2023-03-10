<?php
namespace Gt\Http;

enum RequestMethod {
	case GET;
	case HEAD;
	case POST;
	case PUT;
	case OPTIONS;
	case DELETE;
	case TRACE;

	const ALLOWED_METHODS = [
		self::GET,
		self::HEAD,
		self::POST,
		self::PUT,
		self::OPTIONS,
		self::DELETE,
		self::TRACE,
	];

	public static function filterMethodName(string $name):string {
		$name = strtoupper($name);

		if(!in_array($name, self::ALLOWED_METHODS)) {
			throw new InvalidRequestMethodHttpException($name);
		}

		return $name;
	}
}
