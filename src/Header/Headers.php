<?php
namespace Gt\Http\Header;

use Iterator;

abstract class Headers implements Iterator {
	/** @var HeaderLine[] */
	protected $headerLines = [];
	protected $iteratorIndex = 0;

	public function __construct(array $headerArray) {
		$this->fromArray($headerArray);
	}

	public function asArray():array {
		$array = [];
		foreach($this->headerLines as $header) {
			$array[$header->getName()] = $header->getValues();
		}

		return $this->headerLines;
	}

	public function fromArray(array $headerArray):void {
		foreach($headerArray as $key => $value) {
			if(!is_array($value)) {
				$value = [$value];
			}

			$this->headerLines []= new HeaderLine($key, ...$value);
		}
	}

	public function has(string $name):bool {
		foreach($this->headerLines as $i => $line) {
			if($line->isNamed($name)) {
				return true;
			}
		}

		return false;
	}

	public function add(string $name, string...$values):void {
		// TODO: $values could potentially contain a single string separated by commas; needs splitting.
		$this->headerLines []= new HeaderLine($name,...$values);
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