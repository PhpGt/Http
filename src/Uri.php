<?php /** @noinspection RegExpRedundantEscape */
namespace Gt\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface {
	const DEFAULT_HOST_HTTP = "localhost";
	const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
	const CHAR_SUBDELIMS = '!\$&\'\(\)\*\+,;=';

	protected string $scheme;
	protected string $userInfo;
	protected string $host;
	protected ?string $port;
	protected string $path;
	protected string $query;
	protected string $fragment;

	public function __construct(string $uri = null) {
		if(is_null($uri)) {
			return;
		}

		$parts = parse_url($uri);
		if($parts === false) {
			throw new UriParseErrorException($uri);
		}
		$this->applyParts($parts);
	}

	/**
	 * Return the string representation as a URI reference.
	 *
	 * Depending on which components of the URI are present, the resulting
	 * string is either a full URI or relative reference according to RFC 3986,
	 * Section 4.1. The method concatenates the various components of the URI,
	 * using the appropriate delimiters:
	 *
	 * - If a scheme is present, it MUST be suffixed by ":".
	 * - If an authority is present, it MUST be prefixed by "//".
	 * - The path can be concatenated without delimiters. But there are two
	 *   cases where the path has to be adjusted to make the URI reference
	 *   valid as PHP does not allow to throw an exception in __toString():
	 *     - If the path is rootless and an authority is present, the path MUST
	 *       be prefixed by "/".
	 *     - If the path is starting with more than one "/" and no authority is
	 *       present, the starting slashes MUST be reduced to one.
	 * - If a query is present, it MUST be prefixed by "?".
	 * - If a fragment is present, it MUST be prefixed by "#".
	 *
	 * @see http://tools.ietf.org/html/rfc3986#section-4.1
	 * @return string
	 */
	public function __toString():string {
		$uri = "";
		$scheme = $this->getScheme();
		if(strlen($scheme) > 0) {
			$uri .= $scheme;
			$uri .= ":";
		}

		if(strlen($this->getAuthority()) > 0
		|| $scheme === "file") {
			$uri .= "//";
			$uri .= $this->getAuthority();
		}

		$uri .= $this->getPath();

		if(strlen($this->getQuery()) > 0) {
			$uri .= "?";
			$uri .= $this->getQuery();
		}

		if(strlen($this->getFragment()) > 0) {
			$uri .= "#";
			$uri .= $this->getFragment();
		}

		return $uri;
	}

	/** @param array<string, int|string> $parts */
	public function applyParts(array $parts):void {
		$this->scheme = $this->filterScheme((string)($parts["scheme"] ?? ""));

		$this->userInfo = $this->filterUserInfo(
			(string)($parts["user"] ?? ""),
			(string)($parts["pass"] ?? ""),
		);

		$this->host = $this->filterHost((string)($parts["host"] ?? ""));
		$this->port = $this->filterPort(isset($parts["port"]) ? (int)$parts["port"] : null);
		$this->path = $this->filterPath((string)($parts["path"] ?? ""));
		$this->query = $this->filterQueryAndFragment((string)($parts["query"] ?? ""));
		$this->fragment = $this->filterQueryAndFragment((string)($parts["fragment"] ?? ""));
		$this->setDefaults();
	}

	protected function filterScheme(string $scheme):string {
		return strtolower($scheme);
	}

	protected function filterHost(string $host):string {
		return strtolower($host);
	}

	protected function filterPort(int $port = null):?string {
		if(is_null($port)) {
			return null;
		}

		if($port < 1 || $port > 0xffff) {
			throw new PortOutOfBoundsException((string)$port);
		}

		return (string)$port;
	}

	protected function filterPath(string $path):string {
		/** @noinspection RegExpUnnecessaryNonCapturingGroup */
		/** @noinspection RegExpDuplicateCharacterInClass */
		return preg_replace_callback(
			'/(?:[^'
			. self::CHAR_UNRESERVED
			. self::CHAR_SUBDELIMS
			. '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
			[$this, 'rawurlencodeMatchZero'],
			$path
		);
	}

	protected function filterQueryAndFragment(string $query):string {
		/** @noinspection RegExpUnnecessaryNonCapturingGroup */
		/** @noinspection RegExpDuplicateCharacterInClass */
		return preg_replace_callback(
			'/(?:[^'
				. self::CHAR_UNRESERVED
				. self::CHAR_SUBDELIMS
				. '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
			[$this, 'rawurlencodeMatchZero'],
			$query
		);
	}

	/** @param array<string> $match */
	protected function rawurlencodeMatchZero(array $match):string {
		return rawurlencode($match[0]);
	}

	protected function filterUserInfo(string $user, string $pass = null):string {
		$userInfo = $user;

		if(strlen($pass ?? "") > 0) {
			$userInfo .= ":";
			$userInfo .= $pass;
		}

		return $userInfo;
	}

	/**
	 * Retrieve the scheme component of the URI.
	 *
	 * If no scheme is present, this method MUST return an empty string.
	 *
	 * The value returned MUST be normalized to lowercase, per RFC 3986
	 * Section 3.1.
	 *
	 * The trailing ":" character is not part of the scheme and MUST NOT be
	 * added.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-3.1
	 * @return string The URI scheme.
	 */
	public function getScheme():string {
		return $this->scheme ?? "";
	}

	/**
	 * Retrieve the authority component of the URI.
	 *
	 * If no authority information is present, this method MUST return an empty
	 * string.
	 *
	 * The authority syntax of the URI is:
	 *
	 * <pre>
	 * [user-info@]host[:port]
	 * </pre>
	 *
	 * If the port component is not set or is the standard port for the current
	 * scheme, it SHOULD NOT be included.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-3.2
	 * @return string The URI authority, in "[user-info@]host[:port]" format.
	 */
	public function getAuthority():string {
		$authority = "";

		if(strlen($this->getUserInfo()) > 0) {
			$authority .= $this->getUserInfo();
			$authority .= "@";
		}

		$authority .= $this->getHost();

		if(!$this->isDefaultPort()) {
			$authority .= ":";
			$authority .= $this->getPort();
		}

		return $authority;
	}

	/**
	 * Retrieve the user information component of the URI.
	 *
	 * If no user information is present, this method MUST return an empty
	 * string.
	 *
	 * If a user is present in the URI, this will return that value;
	 * additionally, if the password is also present, it will be appended to the
	 * user value, with a colon (":") separating the values.
	 *
	 * The trailing "@" character is not part of the user information and MUST
	 * NOT be added.
	 *
	 * @return string The URI user information, in "username[:password]" format.
	 */
	public function getUserInfo():string {
		return $this->userInfo ?? "";
	}

	/**
	 * Retrieve the host component of the URI.
	 *
	 * If no host is present, this method MUST return an empty string.
	 *
	 * The value returned MUST be normalized to lowercase, per RFC 3986
	 * Section 3.2.2.
	 *
	 * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
	 * @return string The URI host.
	 */
	public function getHost():string {
		return $this->host ?? "";
	}

	/**
	 * Retrieve the port component of the URI.
	 *
	 * If a port is present, and it is non-standard for the current scheme,
	 * this method MUST return it as an integer. If the port is the standard port
	 * used with the current scheme, this method SHOULD return null.
	 *
	 * If no port is present, and no scheme is present, this method MUST return
	 * a null value.
	 *
	 * If no port is present, but a scheme is present, this method MAY return
	 * the standard port for that scheme, but SHOULD return null.
	 *
	 * @return null|int The URI port.
	 */
	public function getPort():?int {
		if($this->isDefaultPort()) {
			return null;
		}

		return (int)$this->port;
	}

	/**
	 * Retrieve the path component of the URI.
	 *
	 * The path can either be empty or absolute (starting with a slash) or
	 * rootless (not starting with a slash). Implementations MUST support all
	 * three syntaxes.
	 *
	 * Normally, the empty path "" and absolute path "/" are considered equal as
	 * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
	 * do this normalization because in contexts with a trimmed base path, e.g.
	 * the front controller, this difference becomes significant. It's the task
	 * of the user to handle both "" and "/".
	 *
	 * The value returned MUST be percent-encoded, but MUST NOT double-encode
	 * any characters. To determine what characters to encode, please refer to
	 * RFC 3986, Sections 2 and 3.3.
	 *
	 * As an example, if the value should include a slash ("/") not intended as
	 * delimiter between path segments, that value MUST be passed in encoded
	 * form (e.g., "%2F") to the instance.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-2
	 * @see https://tools.ietf.org/html/rfc3986#section-3.3
	 * @return string The URI path.
	 */
	public function getPath():string {
		return $this->path ?? "";
	}

	/**
	 * Retrieve the query string of the URI.
	 *
	 * If no query string is present, this method MUST return an empty string.
	 *
	 * The leading "?" character is not part of the query and MUST NOT be
	 * added.
	 *
	 * The value returned MUST be percent-encoded, but MUST NOT double-encode
	 * any characters. To determine what characters to encode, please refer to
	 * RFC 3986, Sections 2 and 3.4.
	 *
	 * As an example, if a value in a key/value pair of the query string should
	 * include an ampersand ("&") not intended as a delimiter between values,
	 * that value MUST be passed in encoded form (e.g., "%26") to the instance.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-2
	 * @see https://tools.ietf.org/html/rfc3986#section-3.4
	 * @return string The URI query string.
	 */
	public function getQuery():string {
		return $this->query ?? "";
	}

	public function getQueryValue(string $key):?string {
		parse_str($this->getQuery(), $queryVariables);
		return $queryVariables[$key] ?? null;
	}

	/**
	 * Retrieve the fragment component of the URI.
	 *
	 * If no fragment is present, this method MUST return an empty string.
	 *
	 * The leading "#" character is not part of the fragment and MUST NOT be
	 * added.
	 *
	 * The value returned MUST be percent-encoded, but MUST NOT double-encode
	 * any characters. To determine what characters to encode, please refer to
	 * RFC 3986, Sections 2 and 3.5.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-2
	 * @see https://tools.ietf.org/html/rfc3986#section-3.5
	 * @return string The URI fragment.
	 */
	public function getFragment():string {
		return $this->fragment ?? "";
	}

	/**
	 * Return an instance with the specified scheme.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified scheme.
	 *
	 * Implementations MUST support the schemes "http" and "https" case
	 * insensitively, and MAY accommodate other schemes if required.
	 *
	 * An empty scheme is equivalent to removing the scheme.
	 *
	 * @param string $scheme The scheme to use with the new instance.
	 * @return static A new instance with the specified scheme.
	 * @throws InvalidArgumentException for invalid or unsupported schemes.
	 */
	public function withScheme($scheme):self {
		ParameterType::check(__METHOD__, func_get_args(), ["string"]);
		$scheme = $this->filterScheme($scheme);

		$clone = clone $this;
		$clone->scheme = $this->filterScheme($scheme);
		$clone->setDefaults();
		return $clone;
	}

	/**
	 * Return an instance with the specified user information.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified user information.
	 *
	 * Password is optional, but the user information MUST include the
	 * user; an empty string for the user is equivalent to removing user
	 * information.
	 *
	 * @param string $user The username to use for authority.
	 * @param null|string $password The password associated with $user.
	 * @return static A new instance with the specified user information.
	 */
	public function withUserInfo($user, $password = null):self {
		ParameterType::check(__METHOD__, func_get_args(), ["string"]);
		$userInfo = $this->filterUserInfo($user, $password);

		$clone = clone $this;
		$clone->userInfo = $this->filterUserInfo($userInfo);
		$clone->setDefaults();
		return $clone;
	}

	/**
	 * Return an instance with the specified host.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified host.
	 *
	 * An empty host value is equivalent to removing the host.
	 *
	 * @param string $host The hostname to use with the new instance.
	 * @return static A new instance with the specified host.
	 * @throws InvalidArgumentException for invalid hostnames.
	 */
	public function withHost($host):self {
		ParameterType::check(__METHOD__, func_get_args(), ["string"]);
		$host = $this->filterHost($host);

		$clone = clone $this;
		$clone->host = $this->filterHost($host);
		$clone->setDefaults();
		return $clone;
	}

	/**
	 * Return an instance with the specified port.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified port.
	 *
	 * Implementations MUST raise an exception for ports outside the
	 * established TCP and UDP port ranges.
	 *
	 * A null value provided for the port is equivalent to removing the port
	 * information.
	 *
	 * @param null|int $port The port to use with the new instance; a null value
	 *     removes the port information.
	 * @return static A new instance with the specified port.
	 * @throws InvalidArgumentException for invalid ports.
	 */
	public function withPort($port):self {
		ParameterType::check(__METHOD__, func_get_args(), ["?integer"]);
		$port = $this->filterPort($port);

		$clone = clone $this;
		$clone->port = $port;
		$clone->setDefaults();
		return $clone;
	}

	/**
	 * Return an instance with the specified path.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified path.
	 *
	 * The path can either be empty or absolute (starting with a slash) or
	 * rootless (not starting with a slash). Implementations MUST support all
	 * three syntaxes.
	 *
	 * If the path is intended to be domain-relative rather than path relative then
	 * it must begin with a slash ("/"). Paths not starting with a slash ("/")
	 * are assumed to be relative to some base path known to the application or
	 * consumer.
	 *
	 * Users can provide both encoded and decoded path characters.
	 * Implementations ensure the correct encoding as outlined in getPath().
	 *
	 * @param string $path The path to use with the new instance.
	 * @return static A new instance with the specified path.
	 * @throws InvalidArgumentException for invalid paths.
	 */
	public function withPath($path):self {
		ParameterType::check(__METHOD__, func_get_args(), ["string"]);

		$clone = clone $this;
		$clone->path = $this->filterPath($path);
		$clone->setDefaults();
		return $clone;
	}

	/**
	 * Return an instance with the specified query string.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified query string.
	 *
	 * Users can provide both encoded and decoded query characters.
	 * Implementations ensure the correct encoding as outlined in getQuery().
	 *
	 * An empty query string value is equivalent to removing the query string.
	 *
	 * @param string $query The query string to use with the new instance.
	 * @return static A new instance with the specified query string.
	 * @throws InvalidArgumentException for invalid query strings.
	 */
	public function withQuery($query):self {
		ParameterType::check(__METHOD__, func_get_args(), ["string"]);

		$clone = clone $this;
		$clone->query = $this->filterQueryAndFragment($query);
		$clone->setDefaults();
		return $clone;
	}

	public function withQueryValue(string $key, string $value = null):self {
// TODO: Hotspot for refactoring opportunity.
// http_build_query should help simplify all of this messy code.
// Note limitation of http_build_query can be resolved using $replaceQuery below
		$replaceQuery = ["=" => "%3D", "&" => "%26", "^" => "%5E"];
		$current = $this->getQuery();

		if ($current === "") {
			$result = [];
		}
		else {
			$decodedKey = rawurldecode($key);

			$result = array_filter(
				explode("&", $current),
				function($part) use ($decodedKey) {
					return rawurldecode(
						explode("=", $part)[0]
					) !== $decodedKey;
			});
		}
// Query string separators ("=", "&") within the key or value need to be encoded
// (while preventing double-encoding) before setting the query string. All other
// chars that need percent-encoding will be encoded by withQuery().

// This function is taken from Guzzle's Uri implementation, just to get tests
// to pass.
// It must be refactored before v1 release, as it has a major bug as shown here:
		$key = strtr($key, $replaceQuery);
		if ($value !== null) {
			$result[] = $key . "=" . strtr($value, $replaceQuery);
		}
		else {
			$result[] = $key;
		}

		$this->setDefaults();
		return $this->withQuery(implode("&", $result));
	}

	public function withoutQueryValue(string $key):self {
		$current = $this->getQuery();

		$decodedKey = rawurldecode($key);
		$result = array_filter(
			explode("&", $current),
			function($part) use ($decodedKey) {
				return rawurldecode(
					explode("=", $part)[0]
				) !== $decodedKey;
		});

		$this->setDefaults();
		return $this->withQuery(implode("&", $result));
	}

	/**
	 * Return an instance with the specified URI fragment.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified URI fragment.
	 *
	 * Users can provide both encoded and decoded fragment characters.
	 * Implementations ensure the correct encoding as outlined in getFragment().
	 *
	 * An empty fragment value is equivalent to removing the fragment.
	 *
	 * @param string $fragment The fragment to use with the new instance.
	 * @return static A new instance with the specified fragment.
	 */
	public function withFragment($fragment):self {
		ParameterType::check(__METHOD__, func_get_args(), ["string"]);

		$clone = clone $this;
		$clone->fragment = $this->filterQueryAndFragment($fragment);
		$clone->setDefaults();
		return $clone;
	}

	public function isDefaultPort():bool {
		$scheme = $this->getScheme();
		$port = $this->port ?? null;

		if(empty($port)) {
			return true;
		}

		$defaultPortConstant = "\\Gt\\Http\\DefaultPort::$scheme";
		$defaultPort = null;

		if(defined($defaultPortConstant)) {
			$defaultPort = constant($defaultPortConstant);
		}

		return ($defaultPort == $port);
	}

	public function isAbsolute():bool {
		return (
			$this->getScheme() !== ""
		);
	}

	public function isNetworkPathReference():bool {
		return (
			$this->getScheme() === ""
			&& $this->getAuthority() !== ""
		);
	}

	public function isAbsolutePathReference():bool {
		return (
			$this->getScheme() === ""
			&& $this->getAuthority() === ""
			&& isset($this->getPath()[0])
			&& $this->getPath()[0] === "/"
		);

	}

	public function isRelativePathReference():bool {
		return (
			$this->getScheme() === ""
			&& $this->getAuthority() === ""
			&& (!isset($this->getPath()[0]) || $this->getPath()[0] !== '/')
		);
	}

	public function isSameDocumentReference(Uri $baseUri = null):bool {
		if (!is_null($baseUri)) {
			$resolved = UriResolver::resolve($baseUri, $this);

			return (
				$resolved->getScheme() === $baseUri->getScheme()
				&& $resolved->getAuthority() === $baseUri->getAuthority()
				&& $resolved->getPath() === $baseUri->getPath()
				&& $resolved->getQuery() === $baseUri->getQuery()
			);
		}

		return (
			$this->getScheme() === ""
			&& $this->getAuthority() === ""
			&& $this->getPath() === ""
			&& $this->getQuery() === ""
		);
	}

	protected function setDefaults():void {
		if(strlen($this->getHost()) === 0) {
			if($this->getScheme() === "http"
			|| $this->getScheme() === "https") {
				$this->host = self::DEFAULT_HOST_HTTP;
			}
		}

		if($this->getAuthority() === "") {
			if(str_starts_with($this->getPath(), "//")) {
				throw new InvalidArgumentException("The path of a URI without an authority must not start with two slashes \"//\"");
			}
			if(strlen($this->getScheme()) === 0
			&& strpos(explode('/', $this->getPath(), 2)[0], ':')) {
				throw new InvalidArgumentException("A relative URI must not have a path beginning with a segment containing a colon");
			}
		}
		else {
			if(strlen($this->getPath()) > 0
			&& $this->getPath()[0] !== "/") {
				$this->path = "/" . $this->getPath();
			}
		}
	}
}
