<?php
namespace Gt\Http\ResponseStatusException\Redirection;

use Gt\Http\ResponseStatusException\ResponseStatusException;

abstract class RedirectionException extends ResponseStatusException {
	public function __construct(string $redirectTo) {
		parent::__construct($redirectTo);
	}
}
