<?php
namespace Gt\Http;

trait Message {
	/** @var array */
	private $headers = [];
	/** @var array Map of lowercase header name => original name */
	private $headerNames = [];
	/** @var string */
	private $protocol;
	/** @var StreamInterface */
	private $stream;

	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
	 *
	 * @return string HTTP protocol version.
	 */
	public function getProtocolVersion() {
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
	 */
	public function withProtocolVersion($version) {
		if($this->protocol === $version) {
			return $this;
		}
		$new = clone $this;
		$new->protocol = $version;
		return $new;
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
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return bool Returns true if any header names match the given header
	 *     name using a case-insensitive string comparison. Returns false if
	 *     no matching header name is found in the message.
	 */
	public function hasHeader($header) {
		return isset($this->headerNames[strtolower($header)]);
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
	 */
	public function getHeader($header) {
		$header = strtolower($header);
		if(!$this->hasHeader($header)) {
			return [];
		}
		$header = $this->headerNames[$header];
		return $this->headers[$header];
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
	 */
	public function getHeaderLine($header) {
		return implode(", ", $this->getHeader($header));
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
	 */
	public function withHeader($header, $value) {
		if(!is_array($value)) {
			$value = [$value];
		}
		$value = $this->trimHeaderValues($value);
		$normalised = strtolower($header);
		$new = clone $this;
		if($new->hasHeader($header)) {
			unset($new->headers[$new->headerNames[$normalised]]);
		}
		$new->headerNames[$normalised] = $header;
		$new->headers[$header] = $value;
		return $new;
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
	public function withAddedHeader($header, $value) {
		if(!is_array($value)) {
			$value = [$value];
		}
		$value = $this->trimHeaderValues($value);
		$normalised = strtolower($header);
		$new = clone $this;
		if($new->hasHeader($header)) {
			$header = $this->headerNames[$normalised];
			$new->headers[$header] = array_merge($this->headers[$header], $value);
		}
		else {
			$new->headerNames[$normalised] = $header;
			$new->headers[$header] = $value;
		}
		return $new;
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
	 */
	public function withoutHeader($header) {
		if(!$this->hasHeader($header)) {
			return $this;
		}
		$normalised = strtolower($header);
		$header = $this->headerNames[$normalised];
		$new = clone $this;
		unset($new->headers[$header], $new->headerNames[$normalised]);
		return $new;
	}

	public function setHeaders(array $headers) {
		$this->headers = [];
		$this->headerNames = [];
		foreach($headers as $header => $value) {
			if(!is_array($value)) {
				$value = [$value];
			}
			$value = $this->trimHeaderValues($value);
			$normalised = strtolower($header);
			if($this->hasHeader($header)) {
				$header = $this->headerNames[$normalised];
				$this->headers[$header] = array_merge(
					$this->headers[$header],
					$value
				);
			}
			else {
				$this->headerNames[$normalised] = $header;
				$this->headers[$header] = $value;
			}
		}
	}

	/**
	 * Gets the body of the message.
	 *
	 * @return StreamInterface Returns the body as a stream.
	 */
	public function getBody() {
		if(!$this->stream) {
			$this->stream = new Stream("");
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
	public function withBody(StreamInterface $body) {
		if($body === $this->stream) {
			return $this;
		}
		$new = clone $this;
		$new->stream = $body;
		return $new;
	}

	private function trimHeaderValues(array $valueArray) {
		return array_map(function($value) {
			return trim($value, " \t");
		}, $valueArray);
	}

}