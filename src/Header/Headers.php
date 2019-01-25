<?php
namespace Gt\Http\Header;

use Iterator;

class Headers implements Iterator {
	const COMMA_HEADERS = [
// These cookies use commas within the value, so can't be comma separated.
		"Cookie-set",
		"WWW-authenticate",
		"Proxy-authenticate"
	];

	/** @var HeaderLine[] */
	protected $headerLines = [];
	protected $iteratorIndex = 0;

	public function __construct(array $headerArray = []) {
		if(!empty($headerArray)) {
			$this->fromArray($headerArray);
		}
	}

	public function asArray():array {
		$array = [];
		foreach($this->headerLines as $header) {
			$name = $header->getName();
			if(in_array($name, self::COMMA_HEADERS)) {
				$array[$name] = $header->getValuesNewlineSeparated();
			}
			else {
				$array[$name] = $header->getValuesCommaSeparated();
			}
		}

		return $array;
	}

	public function fromArray(array $headerArray):void {
		foreach($headerArray as $key => $value) {
			if(!is_array($value)) {
				$value = [$value];
			}

			$this->headerLines []= new HeaderLine($key, ...$value);
		}
	}

	public function contains(string $name):bool {
		foreach($this->headerLines as $i => $line) {
			if($line->isNamed($name)) {
				return true;
			}
		}

		return false;
	}

	public function add(string $name, string...$values):void {
		$isCommaHeader = false;
		if(strstr($values[0], ",")
		&& in_array($name, self::COMMA_HEADERS)) {
			$isCommaHeader = true;
		}

		$headerLineToAdd = null;
		foreach($this->headerLines as $headerLine) {
			if(!$headerLine->isNamed($name)) {
				continue;
			}

			$headerLineToAdd = $headerLine;
		}

		if(is_null($headerLineToAdd) || $isCommaHeader) {
			$this->headerLines []= new HeaderLine($name,...$values);
		}
		else {
			$headerLineToAdd->addValue(...$values);
		}

	}

	public function set(string $name, string...$value):void {
		$this->remove($name);
		$this->add($name, ...$value);
	}

	public function remove(string $name):void {
		foreach($this->headerLines as $i => $line) {
			if($line->isNamed($name)) {
				unset($this->headerLines[$i]);
			}
		}
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
}