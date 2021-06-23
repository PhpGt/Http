<?php
namespace Gt\Http;

use Gt\Http\Header\RequestHeaders;
use Gt\Input\InputData\Datum\FileUpload;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class ServerRequest extends Request implements ServerRequestInterface {
	protected array $serverData;
	/** @var array<string, mixed> */
	protected array $attributes;
	/** @var array<string, array<string, int|string>> */
	protected array $files;
	/** @var array<string, string> */
	protected array $get;
	/** @var array<string, string> */
	protected array $post;

	/**
	 * @param array<string, string> $serverData From the _SERVER superglobal
	 * @param array<string, array<string, int|string>> $files From the _FILES superglobal
	 */
	public function __construct(
		string $method,
		Uri $uri,
		RequestHeaders $headers,
		array $serverData,
		array $files = [],
		array $get = [],
		array $post = []
	) {
		$this->serverData = $serverData;
		$this->attributes = [];
		$this->files = $files;
		$this->get = $get;
		$this->post = $post;
		parent::__construct($method, $uri, $headers);
	}

	/**
	 * Retrieve server parameters.
	 *
	 * Retrieves data related to the incoming request environment,
	 * typically derived from PHP's $_SERVER superglobal. The data IS NOT
	 * REQUIRED to originate from $_SERVER.
	 *
	 * @return array<string, string>
	 */
	public function getServerParams():array {
		return $this->serverData;
	}

	/**
	 * Retrieve cookies.
	 *
	 * Retrieves cookies sent by the client to the server.
	 *
	 * The data MUST be compatible with the structure of the $_COOKIE
	 * superglobal.
	 *
	 * @return array<string, string>
	 */
	public function getCookieParams():array {
		parse_str(
			strtr(
				$this->serverData["HTTP_COOKIE"] ?? "",
				["&" => "%26", "+" => "%2B", ";" => "&"]
			),
			$cookieParams
		);
		return $cookieParams;
	}

	/**
	 * Return an instance with the specified cookies.
	 *
	 * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
	 * be compatible with the structure of $_COOKIE. Typically, this data will
	 * be injected at instantiation.
	 *
	 * This method MUST NOT update the related Cookie header of the request
	 * instance, nor related values in the server params.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated cookie values.
	 *
	 * @param array<string, string> $cookies Array of key/value pairs representing cookies.
	 * @return static
	 */
	public function withCookieParams(array $cookies):self {
		$clone = clone $this;
		$clone->serverData["HTTP_COOKIE"] = http_build_query(
			$cookies,
			'',
			'; ',
			PHP_QUERY_RFC3986
		);
		return $clone;
	}

	/**
	 * Retrieve query string arguments.
	 *
	 * Retrieves the deserialized query string arguments, if any.
	 *
	 * Note: the query params might not be in sync with the URI or server
	 * params. If you need to ensure you are only getting the original
	 * values, you may need to parse the query string from `getUri()->getQuery()`
	 * or from the `QUERY_STRING` server param.
	 *
	 * @return array<string, string>
	 */
	public function getQueryParams():array {
		parse_str($this->serverData["QUERY_STRING"], $params);
		return $params;
	}

	/**
	 * Return an instance with the specified query string arguments.
	 *
	 * These values SHOULD remain immutable over the course of the incoming
	 * request. They MAY be injected during instantiation, such as from PHP's
	 * $_GET superglobal, or MAY be derived from some other value such as the
	 * URI. In cases where the arguments are parsed from the URI, the data
	 * MUST be compatible with what PHP's parse_str() would return for
	 * purposes of how duplicate query parameters are handled, and how nested
	 * sets are handled.
	 *
	 * Setting query string arguments MUST NOT change the URI stored by the
	 * request, nor the values in the server params.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated query string arguments.
	 *
	 * @param array<string, string> $query Array of query string arguments, typically from
	 *     $_GET.
	 * @return static
	 */
	public function withQueryParams(array $query):self {
		$clone = clone $this;
		$clone->serverData["QUERY_STRING"] = http_build_query($query);
		return $clone;
	}

	/**
	 * Retrieve normalized file upload data.
	 *
	 * This method returns upload metadata in a normalized tree, with each leaf
	 * an instance of Psr\Http\Message\UploadedFileInterface.
	 *
	 * These values MAY be prepared from $_FILES or the message body during
	 * instantiation, or MAY be injected via withUploadedFiles().
	 *
	 * @return array<string, UploadedFileInterface> An array tree of UploadedFileInterface instances; an empty
	 *     array MUST be returned if no data is present.
	 */
	public function getUploadedFiles():array {
		$uploadFileArray = [];

		foreach($this->files as $name => $data) {
			$uploadFileArray[$name] = new FileUpload(
				$data["name"],
				$data["type"] ?? "",
				$data["size"] ?? 0,
				$data["tmp_name"] ?? "",
			);
		}

		return $uploadFileArray;
	}

	/**
	 * Create a new instance with the specified uploaded files.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated body parameters.
	 *
	 * @param array<string, FileUpload> $uploadedFiles An array tree of
	 * UploadedFileInterface instances.
	 * @return self
	 * @throws InvalidArgumentException if an invalid structure is provided.
	 */
	public function withUploadedFiles(array $uploadedFiles):self {
		$clone = clone $this;
		$clone->files = [];
		foreach($uploadedFiles as $name => $uploadedFile) {
			$clone->files[$name] = [
				"name" => $uploadedFile->getOriginalName(),
				"type" => $uploadedFile->getMimeType(),
				"size" => $uploadedFile->getSize(),
				"tmp_name" => $uploadedFile->getRealPath(),
			];
		}

		return $clone;
	}

	/**
	 * Retrieve any parameters provided in the request body.
	 *
	 * If the request Content-Type is either application/x-www-form-urlencoded
	 * or multipart/form-data, and the request method is POST, this method MUST
	 * return the contents of $_POST.
	 *
	 * Otherwise, this method may return any results of deserializing
	 * the request body content; as parsing returns structured content, the
	 * potential types MUST be arrays or objects only. A null value indicates
	 * the absence of body content.
	 *
	 * @return null|array|object The deserialized body parameters, if any.
	 *     These will typically be an array or object.
	 */
	public function getParsedBody():array|object|null {
		return $this->post;
	}

	/**
	 * Return an instance with the specified body parameters.
	 *
	 * These MAY be injected during instantiation.
	 *
	 * If the request Content-Type is either application/x-www-form-urlencoded
	 * or multipart/form-data, and the request method is POST, use this method
	 * ONLY to inject the contents of $_POST.
	 *
	 * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
	 * deserializing the request body content. Deserialization/parsing returns
	 * structured data, and, as such, this method ONLY accepts arrays or objects,
	 * or a null value if nothing was available to parse.
	 *
	 * As an example, if content negotiation determines that the request data
	 * is a JSON payload, this method could be used to create a request
	 * instance with the deserialized parameters.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated body parameters.
	 *
	 * @param null|array|object $data The deserialized body data. This will
	 *     typically be in an array or object.
	 * @return static
	 * @throws \InvalidArgumentException if an unsupported argument type is
	 *     provided.
	 */
	public function withParsedBody($data):self {
		$clone = clone $this;
		$clone->post = (array)$data;
		return $clone;
	}

	/**
	 * Retrieve attributes derived from the request.
	 *
	 * The request "attributes" may be used to allow injection of any
	 * parameters derived from the request: e.g., the results of path
	 * match operations; the results of decrypting cookies; the results of
	 * deserializing non-form-encoded message bodies; etc. Attributes
	 * will be application and request specific, and CAN be mutable.
	 *
	 * @return array<string, mixed> Attributes derived from the request.
	 */
	public function getAttributes():array {
		return $this->attributes;
	}

	/**
	 * Retrieve a single derived request attribute.
	 *
	 * Retrieves a single derived request attribute as described in
	 * getAttributes(). If the attribute has not been previously set, returns
	 * the default value as provided.
	 *
	 * This method obviates the need for a hasAttribute() method, as it allows
	 * specifying a default value to return if the attribute is not found.
	 *
	 * @see getAttributes()
	 * @param string $name The attribute name.
	 * @param mixed $default Default value to return if the attribute does not exist.
	 */
	public function getAttribute($name, $default = null):?string {
		return $this->attributes[$name] ?? $default;
	}

	/**
	 * Return an instance with the specified derived request attribute.
	 *
	 * This method allows setting a single derived request attribute as
	 * described in getAttributes().
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated attribute.
	 *
	 * @see getAttributes()
	 * @param string $name The attribute name.
	 * @param mixed $value The value of the attribute.
	 * @return static
	 */
	public function withAttribute($name, $value):self {
		$clone = clone $this;
		$clone->attributes[$name] = $value;
		return $clone;
	}

	/**
	 * Return an instance that removes the specified derived request attribute.
	 *
	 * This method allows removing a single derived request attribute as
	 * described in getAttributes().
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that removes
	 * the attribute.
	 *
	 * @see getAttributes()
	 * @param string $name The attribute name.
	 * @return static
	 */
	public function withoutAttribute($name):self {
		$clone = clone $this;
		unset($clone->attributes[$name]);
		return $clone;
	}
}
