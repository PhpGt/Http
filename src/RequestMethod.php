<?php
namespace Gt\Http;

class RequestMethod {
	const ALLOWED_METHODS = [
		"GET",
		"HEAD",
		"POST",
		"PUT",
		"OPTIONS",
		"DELETE",
		"TRACE",
	];

	public static function filterMethodName(string $name):string {
		$name = strtoupper($name);

		if(!in_array($name, self::ALLOWED_METHODS)) {
			throw new InvalidRequestMethodException($name);
		}

		return $name;
	}
}