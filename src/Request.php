<?php
namespace Gt\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Gt\Http\Header\RequestHeaders;

/**
 * @property-read string $cache Contains the cache mode of the request (e.g., default, reload, no-cache).
 * @property-read string $context Contains the context of the request (e.g., audio, image, iframe, etc.)
 * @property-read string $credentials Contains the credentials of the request (e.g., omit, same-origin, include). The default is same-origin.
 * @property-read string $destination Returns a string from the RequestDestination enum describing the request's destination. This is a string indicating the type of content being requested.
 * @property-read RequestHeaders $headers Contains the associated Headers object of the request.
 * @property-read string $integrity Contains the subresource integrity value of the request (e.g., sha256-BpfBw7ivV8q2jLiT13fxDYAe2tJllusRSZ273h2nFSE=).
 * @property-read string $method Contains the request's method (GET, POST, etc.)
 * @property-read string $mode Contains the mode of the request (e.g., cors, no-cors, same-origin, navigate.)
 * @property-read string $redirect Contains the mode for how redirects are handled. It may be one of follow, error, or manual.
 * @property-read string $referrer Contains the referrer of the request (e.g., client).
 * @property-read string $referrerPolicy Contains the referrer policy of the request (e.g., no-referrer).
 * @property-read UriInterface $url Contains the URL of the request.
 */
class Request implements RequestInterface {
	use Message;

	protected string $method;
	protected UriInterface $uri;
	protected string $requestTarget;

	public function __construct(
		string $method,
		Uri $uri,
		RequestHeaders $headers = null
	) {
		$this->method = RequestMethod::filterMethodName($method);
		$this->uri = $uri;
		$this->internalHeaders = $headers ?? new RequestHeaders();

		$firstLine = $this->internalHeaders->getFirst();
		$this->protocol = substr(
			$firstLine,
			0,
			strpos($firstLine, " ")
		);
	}

	/**
	 * Retrieves the message's request target.
	 *
	 * Retrieves the message's request-target either as it will appear (for
	 * clients), as it appeared at request (for servers), or as it was
	 * specified for the instance (see withRequestTarget()).
	 *
	 * In most cases, this will be the origin-form of the composed URI,
	 * unless a value was provided to the concrete implementation (see
	 * withRequestTarget() below).
	 *
	 * If no URI is available, and no request-target has been specifically
	 * provided, this method MUST return the string "/".
	 *
	 * @return string
	 */
	public function getRequestTarget():string {
		if(isset($this->requestTarget)) {
			return $this->requestTarget;
		}

		$uri = $this->getUri();
		$requestTarget = $uri->getPath();
		if(empty($requestTarget)) {
			$requestTarget = "/";
		}

		$query = $uri->getQuery();
		if(!empty($query)) {
			$requestTarget .= "?";
		}
		$requestTarget .= $query;

		return $requestTarget;
	}

	/**
	 * Return an instance with the specific request-target.
	 *
	 * If the request needs a non-origin-form request-target — e.g., for
	 * specifying an absolute-form, authority-form, or asterisk-form —
	 * this method may be used to create an instance with the specified
	 * request-target, verbatim.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * changed request target.
	 *
	 * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
	 *     request-target forms allowed in request messages)
	 * @param mixed $requestTarget
	 * @return static
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function withRequestTarget($requestTarget):static {
		$clone = clone $this;
		$clone->requestTarget = $requestTarget;
		return $clone;
	}

	/**
	 * Retrieves the HTTP method of the request.
	 *
	 * @return string Returns the request method.
	 */
	public function getMethod():string {
		return $this->method;
	}

	/**
	 * Return an instance with the provided HTTP method.
	 *
	 * While HTTP method names are typically all uppercase characters, HTTP
	 * method names are case-sensitive and thus implementations SHOULD NOT
	 * modify the given string.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * changed request method.
	 *
	 * @param string $method Case-sensitive method.
	 * @return static
	 * @throws \InvalidArgumentException for invalid HTTP methods.
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function withMethod($method):static  {
		$method = RequestMethod::filterMethodName($method);
		$clone = clone $this;
		$clone->method = $method;
		return $clone;
	}

	/**
	 * Retrieves the URI instance.
	 *
	 * This method MUST return a UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @return UriInterface Returns a UriInterface instance
	 *     representing the URI of the request.
	 */
	public function getUri():UriInterface {
		return $this->uri;
	}

	/**
	 * Returns an instance with the provided URI.
	 *
	 * This method MUST update the Host header of the returned request by
	 * default if the URI contains a host component. If the URI does not
	 * contain a host component, any pre-existing Host header MUST be carried
	 * over to the returned request.
	 *
	 * You can opt-in to preserving the original state of the Host header by
	 * setting `$preserveHost` to `true`. When `$preserveHost` is set to
	 * `true`, this method interacts with the Host header in the following ways:
	 *
	 * - If the Host header is missing or empty, and the new URI contains
	 *   a host component, this method MUST update the Host header in the returned
	 *   request.
	 * - If the Host header is missing or empty, and the new URI does not contain a
	 *   host component, this method MUST NOT update the Host header in the returned
	 *   request.
	 * - If a Host header is present and non-empty, this method MUST NOT update
	 *   the Host header in the returned request.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @param UriInterface $uri New request URI to use.
	 * @param bool $preserveHost Preserve the original state of the Host header.
	 * @return static
	 */
	public function withUri(UriInterface $uri, $preserveHost = false):static {
		$clone = clone $this;

		$host = $uri->getHost();
		if(!empty($host)) {
			if(!$preserveHost
			|| !$this->internalHeaders->contains("Host")) {
				$this->internalHeaders = $this->internalHeaders->withHeader(
					"Host",
					$host
				);
			}
		}

		$clone->uri = $uri;
		return $clone;
	}
}