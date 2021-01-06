<?php
namespace Gt\Http;

trait Getter {
	public function __get(string $name):mixed {
		$internalFunc = "prop_get_$name";
		if(method_exists($this, $internalFunc)) {
			return call_user_func($internalFunc);
		}

		trigger_error(
			"Undefined property: " . __CLASS__ . "::$name",
			E_USER_WARNING
		);
	}
}