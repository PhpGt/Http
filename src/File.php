<?php
namespace Gt\Http;

class File extends Blob {
	/**
	 * @param array<ArrayBuffer|Blob|string> $bits
	 * @param string $name
	 * @param array<string, string> $options
	 */
	public function __construct(
		array $bits,
		string $name,
		array $options = [],
	) {
		parent::__construct($bits, $options);
		$this->name = $name;
	}
}
