<?php
namespace Gt\Http\Header;

class HeaderLine {
	protected string $originalNameCase;
	protected string $name;
	/** @var string[] */
	protected array $values;

	public function __construct(string $name, string...$values) {
		$this->originalNameCase = $name;
		$this->name = strtolower($name);
		$this->values = $values;
	}

	public function __toString():string {
		if(in_array($this->name, Headers::COMMA_HEADERS)) {
			return $this->getValuesNewlineSeparated();
		}
		else {
			return $this->getValuesCommaSeparated();
		}
	}

	public function withValue(string...$values):static {
		$clone = clone $this;
		$clone->values = [];
		return $clone->withAddedValue(...$values);
	}

	public function withAddedValue(string...$values):static {
		$clone = clone $this;
		foreach($values as $v) {
			array_push($clone->values, $v);
		}
		return $clone;
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

	public function getValuesNewlineSeparated():string {
		return implode("\n", $this->values);
	}

	public function isNamed(string $name):bool {
		return $this->name === strtolower($name);
	}
}