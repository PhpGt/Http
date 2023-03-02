<?php
namespace Gt\Http;

use Gt\Http\Header\ResponseHeaders;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @property ResponseHeaders $headers
 */
class Response implements ResponseInterface {
	use Message;

	/** @var null|callable */
	protected $exitCallback;

	public function __construct(
		private ?int $statusCode = null,
		ResponseHeaders $headers = null,
		private ?Request $request = null,
	) {
		$this->headers = $headers ?? new ResponseHeaders();
		$this->stream = new Stream();
	}

	public function setExitCallback(callable $callback):void {
		$this->exitCallback = $callback;
	}

	public function reload(bool $keepQuery = true):void {
		$uri = $this->request?->getUri() ?? new Uri();
		$uri = $uri->withPath("./");
		if(!$keepQuery) {
			$uri = $uri->withQuery("");
		}
		$this->redirect($uri);
	}

	public function redirect(string|UriInterface $uri, int $statusCode = 303):void {
		$this->statusCode = $statusCode;
		$this->headers->set("Location", (string)$uri);
		if(isset($this->exitCallback)) {
			call_user_func($this->exitCallback);
		}
	}

	/**
	 * Gets the response status code.
	 *
	 * The status code is a 3-digit integer result code of the server's attempt
	 * to understand and satisfy the request.
	 *
	 * @return int Status code.
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * Return an instance with the specified status code and, optionally, reason phrase.
	 *
	 * If no reason phrase is specified, implementations MAY choose to default
	 * to the RFC 7231 or IANA recommended reason phrase for the response's
	 * status code.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated status and reason phrase.
	 *
	 * @link http://tools.ietf.org/html/rfc7231#section-6
	 * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 * @param int $code The 3-digit integer result code to set.
	 * @param string $reasonPhrase The reason phrase to use with the
	 *     provided status code; if none is provided, implementations MAY
	 *     use the defaults as suggested in the HTTP specification.
	 * @return static
	 * @throws \InvalidArgumentException For invalid status code arguments.
	 */
	public function withStatus($code, $reasonPhrase = '') {
		$clone = clone $this;
		$clone->statusCode = $code;
		return $clone;
	}

	/**
	 * Gets the response reason phrase associated with the status code.
	 *
	 * Because a reason phrase is not a required element in a response
	 * status line, the reason phrase value MAY be null. Implementations MAY
	 * choose to return the default RFC 7231 recommended reason phrase (or those
	 * listed in the IANA HTTP Status Code Registry) for the response's
	 * status code.
	 *
	 * @link http://tools.ietf.org/html/rfc7231#section-6
	 * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 * @return string Reason phrase; must return an empty string if none present.
	 */
	public function getReasonPhrase() {
		return StatusCode::REASON_PHRASE[$this->statusCode];
	}

	public function getResponseHeaders():ResponseHeaders {
		return $this->headers;
	}
}
