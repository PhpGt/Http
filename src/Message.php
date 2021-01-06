<?php
namespace Gt\Http;

use Gt\Http\Header\Headers;
use Psr\Http\Message\StreamInterface;

trait Message {
	protected Headers $headers;
	protected string $protocol;
	protected StreamInterface $stream;

	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
	 *
	 * @return string HTTP protocol version.
	 */
	public function getProtocolVersion():string {
		if(strstr($this->protocol, "/")) {
			return substr(
				$this->protocol,
				strpos($this->protocol, "/") + 1
			);
		}

		return $this->protocol;
	}

	/**
	 * Return an instance with the specified HTTP protocol version.
	 *
	 * The version string MUST contain only the HTTP version number (e.g.,
	 * "1.1", "1.0").
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new protocol version.
	 *
	 * @param string $version HTTP protocol version
	 * @return static
	 * @throws InvalidProtocolHttpException
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function withProtocolVersion($version):static {
		if(!is_numeric($version)) {
			throw new InvalidProtocolHttpException($version);
		}

		$clone = clone $this;

		if(strstr($clone->protocol, "/")) {
			$versionStartPos = strpos(
					$clone->protocol,
					"/"
				) + 1;
			$versionEndPos = strpos(
				$clone->protocol,
				" "
			);

			$protocol = "";
			$protocol .= substr(
				$clone->protocol,
				0,
				$versionStartPos
			);
			$protocol .= $version;
			$protocol .= substr(
				$clone->protocol,
				$versionEndPos
			);
			$clone->protocol = $protocol;
		}
		else {
			$clone->protocol = $version;
		}

		return $clone;
	}

	/**
	 * Retrieves all message header values.
	 *
	 * The keys represent the header name as it will be sent over the wire, and
	 * each value is an array of strings associated with the header.
	 *
	 *     // Represent the headers as a string
	 *     foreach ($message->getHeaders() as $name => $values) {
	 *         echo $name . ": " . implode(", ", $values);
	 *     }
	 *
	 *     // Emit headers iteratively:
	 *     foreach ($message->getHeaders() as $name => $values) {
	 *         foreach ($values as $value) {
	 *             header(sprintf('%s: %s', $name, $value), false);
	 *         }
	 *     }
	 *
	 * While header names are not case-sensitive, getHeaders() will preserve the
	 * exact case in which headers were originally specified.
	 *
	 * @return string[][] Returns an associative array of the message's headers. Each
	 *     key MUST be a header name, and each value MUST be an array of strings
	 *     for that header.
	 */
	public function getHeaders():array {
		return $this->headers->asArray();
	}

	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return bool Returns true if any header names match the given header
	 *     name using a case-insensitive string comparison. Returns false if
	 *     no matching header name is found in the message.
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function hasHeader($name):bool {
		return $this->headers->contains($name);
	}

	/**
	 * Retrieves a message header value by the given case-insensitive name.
	 *
	 * This method returns an array of all the header values of the given
	 * case-insensitive header name.
	 *
	 * If the header does not appear in the message, this method MUST return an
	 * empty array.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string[] An array of string values as provided for the given
	 *    header. If the header does not appear in the message, this method MUST
	 *    return an empty array.
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function getHeader($name):array {
		return $this->headers->getAll($name);
	}

	/**
	 * Retrieves a comma-separated string of the values for a single header.
	 *
	 * This method returns all of the header values of the given
	 * case-insensitive header name as a string concatenated together using
	 * a comma.
	 *
	 * NOTE: Not all header values may be appropriately represented using
	 * comma concatenation. For such headers, use getHeader() instead
	 * and supply your own delimiter when concatenating.
	 *
	 * If the header does not appear in the message, this method MUST return
	 * an empty string.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string A string of values as provided for the given header
	 *    concatenated together using a comma. If the header does not appear in
	 *    the message, this method MUST return an empty string.
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function getHeaderLine($name):string {
		$header = $this->headers->get($name);

		if($header) {
			return $header->getValuesCommaSeparated();
		}
		else {
			return "";
		}
	}

	/**
	 * Return an instance with the provided value replacing the specified header.
	 *
	 * While header names are case-insensitive, the casing of the header will
	 * be preserved by this function, and returned from getHeaders().
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new and/or updated header and value.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @param string|string[] $value Header value(s).
	 * @return static
	 * @throws \InvalidArgumentException for invalid header names or values.
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function withHeader($name, $value):static {
		if(!is_array($value)) {
			$value = [$value];
		}

		$clone = clone $this;
		$clone->headers = $clone->headers->withHeader($name, ...$value);
		return $clone;
	}

	/**
	 * Return an instance with the specified header appended with the given value.
	 *
	 * Existing values for the specified header will be maintained. The new
	 * value(s) will be appended to the existing list. If the header did not
	 * exist previously, it will be added.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new header and/or value.
	 *
	 * @param string $name Case-insensitive header field name to add.
	 * @param string|string[] $value Header value(s).
	 * @return static
	 * @throws \InvalidArgumentException for invalid header names or values.
	 */
	public function withAddedHeader($name, $value) {
		if(!is_array($value)) {
			$value = [$value];
		}

		$clone = clone $this;
		$clone->headers = $clone->headers->withAddedHeaderValue($name, ...$value);
		return $clone;
	}

	/**
	 * Return an instance without the specified header.
	 *
	 * Header resolution MUST be done without case-sensitivity.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that removes
	 * the named header.
	 *
	 * @param string $name Case-insensitive header field name to remove.
	 * @return static
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function withoutHeader($name):static {
		$clone = clone $this;
		$clone->headers = $clone->headers->withoutHeader($name);
		return $clone;
	}

	/**
	 * Gets the body of the message.
	 *
	 * @return StreamInterface Returns the body as a stream.
	 */
	public function getBody():StreamInterface {
		if(!$this->stream) {
			$this->stream = new Stream("php://memory");
		}
		return $this->stream;
	}

	/**
	 * Return an instance with the specified message body.
	 *
	 * The body MUST be a StreamInterface object.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return a new instance that has the
	 * new body stream.
	 *
	 * @param StreamInterface $body Body.
	 * @return static
	 * @throws \InvalidArgumentException When the body is not valid.
	 */
	public function withBody(StreamInterface $body):static {
		$clone = clone $this;
		$clone->stream = $body;
		return $clone;
	}

	private function trimHeaderValues(array $valueArray):array {
		return array_map(function($value) {
			return trim($value, " \t");
		}, $valueArray);
	}

}