<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\HttpException;

abstract class ResponseStatusException extends HttpException {
	public function __construct(string $message = "") {
		parent::__construct(
			$message,
			$this->getHttpCode(),
			null
		);
	}

	abstract public function getHttpCode():int;
}
