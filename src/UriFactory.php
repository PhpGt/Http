<?php
namespace Gt\Http;

use Psr\Http\Message\UriInterface;

class UriFactory {
	public static function createFromParts(array $parts):Uri {
		$uri = new Uri();
		$uri->applyParts($parts);
		return $uri;
	}

	/**
	 * Composes a URI reference string from its various components.
	 *
	 * Usually this method does not need to be called manually but instead is used indirectly via
	 * `Psr\Http\Message\UriInterface::__toString`.
	 *
	 * PSR-7 UriInterface treats an empty component the same as a missing component as
	 * getQuery(), getFragment() etc. always return a string. This explains the slight
	 * difference to RFC 3986 Section 5.3.
	 *
	 * Another adjustment is that the authority separator is added even when the authority is missing/empty
	 * for the "file" scheme. This is because PHP stream functions like `file_get_contents` only work with
	 * `file:///myfile` but not with `file:/myfile` although they are equivalent according to RFC 3986. But
	 * `file:///` is the more common syntax for the file scheme anyway (Chrome for example redirects to
	 * that format).
	 *
	 * @link https://tools.ietf.org/html/rfc3986#section-5.3
	 */
	public static function composeFromComponents(
		string $scheme = null,
		string $authority = null,
		string $path = null,
		string $query = null,
		string $fragment = null
	):Uri {
		$uri = "";

		if(!is_null($scheme )
		&& strlen($scheme) > 0) {
			$uri .= $scheme . ":";
		}

		if((!is_null($authority)
		&& strlen($authority) > 0)
		|| $scheme === "file") {
			$uri .= "//" . $authority;
		}

		$uri .= $path;

		if(!is_null($query)
		&& strlen($query) > 0) {
			$uri .= "?";
			$uri .= $query;
		}

		if(!is_null($fragment)
		&& strlen($fragment) > 0) {
			$uri .= "#";
			$uri .= $fragment;
		}

		return new Uri($uri);
	}
}