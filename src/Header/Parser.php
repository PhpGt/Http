<?php
namespace Gt\Http\Header;

class Parser {
	protected $rawHeaders;

	public function __construct(string $rawHeaders) {
		$this->rawHeaders = $rawHeaders;
	}

	public function getProtocolVersion():string {
		return $this->pregMatchProtocol("version");
	}

	public function getStatusCode():int {
		return $this->pregMatchProtocol("code");
	}

	public function getKeyValues():array {
		$keyValues = [];

		foreach(explode("\n", $this->rawHeaders) as $i => $h) {
			if($i === 0) {
				continue;
			}

			$h = trim($h);
			list(
				$key,
				$value
			) = explode(":", $h, 2);

			$keyValues[$key] = $value;
		}

		return $keyValues;
	}

	protected function pregMatchProtocol(string $matchName) {
		$headerLine = strtok($this->rawHeaders, "\n");
		preg_match(
			"/HTTP\/(?P<version>.+)\s*(?P<code>\d+)?/",
			$headerLine,
			$matches
		);
		return $matches[$matchName];
	}
}