<?php
namespace Gt\Http\ResponseStatusException\Redirection;

use Gt\Http\ResponseStatusException\AbstractResponseStatusException;

abstract class AbstractRedirectionException extends AbstractResponseStatusException {
	public function __construct(string $redirectTo) {
		parent::__construct($redirectTo);
	}
}