<?php
namespace Gt\Http;

class FormData {
	/** @var array */
	protected $data;

	public function __construct(array $data = []) {
		$this->data = $data;
	}

	public function append(
		string $key,
		string $value,
		string $filename = null
	):void {

	}

	public function set(
		string $name,
		string $value,
		string $filename = null
	):void {

	}

	public function delete(string $name):void {

	}

	public function entries():array {

	}

	public function get(string $name):?string {

	}

	public function getAll(string $name):array {

	}

	/** For consistency with naming of other Web APIs. */
	public function contains(string $name):bool {

	}

	public function has(string $name):bool {

	}

	public function keys():array {

	}

	public function values():array {

	}
}