<?php
namespace Gt\Http;

class HeaderLine {
	protected $originalNameCase;
	protected $name;
	protected $values;

	public function __construct(string $name, string...$values) {
		$this->originalNameCase = $name;
		$this->name = strtolower($name);
		$this->values = $values;
	}

	public function getName():string {
		return $this->originalNameCase;
	}

	public function getValue(int $position = 0):?string {
		return $this->values[$position] ?? null;
	}

	public function getValues():array {
		return $this->values;
	}

	public function getValuesCommaSeparated():string {
		return implode(",", $this->values);
	}

	public function isNamed(string $name):bool {
		return $this->name === strtolower($name);
	}
}