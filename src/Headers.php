<?php
namespace Gt\Http;

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

	public function add(string $name, string...$values) {
		// TODO: $values could potentially contain a single string separated by colons; needs splitting.
		var_dump($name, $values);die();
		$this->headerLines []= new HeaderLine($name,...$values);
	}

// Iterator ----------------------------------------------------------------------------------------
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