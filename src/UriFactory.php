<?php
namespace Gt\Http;

class UriFactory {
	public static function createFromParts(array $parts):Uri {
		$uri = new Uri();
		$uri->applyParts($parts);
		return $uri;
	}
}