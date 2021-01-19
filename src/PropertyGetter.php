<?php
namespace Gt\Http;

trait PropertyGetter {
	public function __get(string $name):mixed {
		$methodName = "__get" . ucfirst($name);
		if(method_exists($this, $methodName)) {
			return call_user_func([$this, $methodName]);
		}

		trigger_error(
			"Undefined property: "
			. get_class($this)
			. "::$name"
		);
	}
}