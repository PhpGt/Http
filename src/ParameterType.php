<?php
namespace Gt\Http;

use TypeError;

/**
 * Allows throwing TypeErrors on parameters of the wrong type when implementing an interface written
 * before PHP 7's type hints were introduced.
 */
class ParameterType {
	/**
	 * @param array<string> $parameters
	 * @param array<string> $types Array of type names
	 */
	public function __construct(
		string $method,
		array $parameters,
		array $types,
	) {
		foreach($types as $i => $type) {
			$nullable = false;
			if($type[0] === "?") {
				$type = substr($type, 1);
				$nullable = true;
			}

			$actualType = gettype($parameters[$i]);
			if($type === $actualType) {
				continue;
			}

			if($nullable && $actualType === "NULL") {
				continue;
			}

			$num = $i + 1;
			$message = "Argument $num passed to $method must be of the type ";
			if($nullable) {
				$message .= "?";
			}
			$message .= "$type, $actualType given.";

			throw new TypeError($message);
		}
	}
}
