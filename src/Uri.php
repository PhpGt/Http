<?php
namespace Gt\Http;

class Uri extends Url {
	public function __construct(string $uri = null) {
		trigger_error(
			"Uri has been deprecated - use Url instead. "
			. "This is to maintain web standards compliance. "
			. "See https://developer.mozilla.org/en-US/docs/Web/API/URL"
		);
		parent::__construct($uri);
	}
}
