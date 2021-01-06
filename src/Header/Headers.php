<?php
namespace Gt\Http\Header;

use Countable;
use Iterator;

class Headers implements Iterator, Countable {
	const COMMA_HEADERS = [
// These cookies use commas within the value, so can't be comma separated.
		"cookie-set",
		"www-authenticate",
		"proxy-authenticate"
	];

	/** @var HeaderLine[] */
	protected array $headerLines = [];
	protected int $iteratorIndex = 0;

	public function __construct(array $headerArray = []) {
		foreach($headerArray as $key => $value) {
			if(!is_array($value)) {
				$value = [$value];
			}

			array_push(
				$this->headerLines,
				new HeaderLine($key, ...$value)
			);
		}
	}

	public function asArray():array {
		$array = [];
		foreach($this->headerLines as $header) {
			$name = $header->getName();
			if(in_array(strtolower($name), self::COMMA_HEADERS)) {
				$array[$name] = $header->getValuesNewlineSeparated();
			}
			else {
				$array[$name] = $header->getValuesCommaSeparated();
			}
		}

		return $array;
	}

	public function contains(string $name):bool {
		foreach($this->headerLines as $i => $line) {
			if($line->isNamed($name)) {
				return true;
			}
		}

		return false;
	}

	public function withHeader(string $name, string...$value):static {
		$clone = clone $this;
		$clone->headerLines[$name] = new HeaderLine($name, ...$value);
		return $clone;
	}

	public function withAddedHeaderValue(string $name, string...$values):static {
		$clone = clone $this;
		if(isset($this->headerLines[$name])) {
			$clone->headerLines[$name] = $clone->headerLines[$name]
				->withAddedValue(...$values);
		}
		else {
			$clone = $clone->withHeader($name, ...$values);
		}

		return $clone;
	}

	public function withoutHeader(string $name):static {
		$clone = clone $this;

		foreach($clone->headerLines as $i => $line) {
			if($line->isNamed($name)) {
				unset($clone->headerLines[$i]);
			}
		}

		return $clone;
	}

	public function get(string $name):?HeaderLine {
		foreach($this->headerLines as $i => $line) {
			if($line->isNamed($name)) {
				return $line;
			}
		}

		return null;
	}

	public function getAll(string $name):?array {
		foreach($this->headerLines as $i => $line) {
			if($line->isNamed($name)) {
				return $line->getValues();
			}
		}

		return null;
	}

	public function getFirst():string {
		return $this->headerLines[0] ?? "";
	}

	public function current():HeaderLine {
		return $this->headerLines[$this->iteratorIndex];
	}

	public function next():void {
		$this->iteratorIndex++;
	}

	public function key():int {
		return $this->iteratorIndex;
	}

	public function valid():bool {
		return isset($this->headerLines[$this->iteratorIndex]);
	}

	public function rewind():void {
		$this->iteratorIndex = 0;
	}

	public function count():int {
		return count($this->headerLines);
	}
}