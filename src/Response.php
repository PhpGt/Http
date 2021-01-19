<?php
namespace Gt\Http;

use Gt\Http\Header\ResponseHeaders;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @property-read ResponseHeaders $headers The Headers object associated with the response.
 * @property-read bool $ok A boolean indicating whether the response was successful (status in the range 200–299) or not.
 * @property-read bool $redirected Indicates whether or not the response is the result of a redirect (that is, its URL list has more than one entry).
 * @property-read int $status The status code of the response. (This will be 200 for a success).
 * @property-read string $statusText The status message corresponding to the status code. (e.g., OK for 200).
 * @property-read string $type The status message corresponding to the status code. (e.g., OK for 200).
 * @property-read Uri $url The status message corresponding to the status code. (e.g., OK for 200).
 * @property-read bool $useFinalURL A boolean indicating whether this is the final URL of the response.
 * @property-read bool $bodyUsed Stores a Boolean that declares whether the body has been used in a response yet.
 */
class Response implements ResponseInterface {
	use Message;

	protected ?int $statusCode;

	public function __construct(
		int $status = null,
		ResponseHeaders $headers = null,
		string $body = null
	) {
		$this->statusCode = $status;
		$this->internalHeaders = $headers ?? new ResponseHeaders();
		$this->stream = new Stream();

		if($body) {
			$this->stream->write($body);
		}
	}

	public function __get(string $name):mixed {
		$methodName = "prop_get_$name";
		if(method_exists($this, $methodName)) {
			return call_user_func($methodName);
		}

		trigger_error(
			"Undefined property: "
			. get_class($this)
			. "::$name"
		);
	}

	public function prop_get_ok():bool {
		return $this->status >= 200 && $this->status <= 299;
	}

	public function prop_get_redirected():bool {

	}

	public function prop_get_status():int {

	}

	public function prop_get_statusText():string {

	}

	public function prop_get_type():string {

	}

	public function prop_get_url():Uri {

	}

	public function prop_get_useFinalUrl():bool {

	}

	public function prop_get_bodyUsed():bool {

	}

	public function redirect(string $uri, int $status = 302):static {
		$clone = clone $this;
		$clone = $clone->withStatus($status);
		$clone->internalHeaders = $clone->internalHeaders->withHeader("Location: $uri");
		return $clone;
	}

	/**
	 * Gets the response status code.
	 *
	 * The status code is a 3-digit integer result code of the server's attempt
	 * to understand and satisfy the request.
	 *
	 * @return int Status code.
	 */
	public function getStatusCode():?int {
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
	public function withStatus($code, $reasonPhrase = ''):static {
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
	public function getReasonPhrase():?string {
		return StatusCode::REASON_PHRASE[$this->statusCode] ?? null;
	}

	public function getResponseHeaders():ResponseHeaders {
		return $this->internalHeaders;
	}
}