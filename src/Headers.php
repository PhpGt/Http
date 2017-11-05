<?php
namespace Gt\Http;

abstract class Headers {
	protected $headerArray;

	public function __construct(array $headerArray) {
		$this->fromArray($headerArray);
	}

	public function asArray():array {
		return $this->headerArray;
	}

	public function fromArray(array $headerArray):void {
		$this->headerArray = $headerArray;
	}
}