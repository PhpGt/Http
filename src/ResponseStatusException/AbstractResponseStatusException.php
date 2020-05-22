<?php
namespace Gt\Http\ResponseStatusException;

use Gt\Http\HttpException;

abstract class AbstractResponseStatusException extends HttpException {
	public function __construct(string $message = "") {
		parent::__construct(
			$message,
			$this->getHttpCode(),
			null
		);
	}

	abstract protected function getHttpCode():int;
}