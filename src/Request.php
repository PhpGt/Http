<?php
namespace Gt\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Gt\Http\Header\RequestHeaders;

class Request implements RequestInterface {
	use Message;

	protected string $method;
	protected UriInterface $uri;
	protected string $requestTarget;

	/** @SuppressWarnings("StaticAccess") */
	public function __construct(
		string $method,
		UriInterface $uri,
		RequestHeaders $headers
	) {
		$this->method = RequestMethod::filterMethodName($method);
		$this->uri = $uri;
		$this->headers = $headers;

		$firstLine = $this->headers->getFirst();
		$this->protocol = substr(
			$firstLine,
			0,
			strpos($firstLine, " ") ?: 0
		);
	}

	/** @inheritDoc */
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

	/** @inheritDoc */
	public function withRequestTarget($requestTarget):self {
		$clone = clone $this;
		$clone->requestTarget = $requestTarget;
		return $clone;
	}

	/** @inheritDoc */
	public function getMethod():string {
		return $this->method;
	}

	/**
	 * @inheritDoc
	 * @SuppressWarnings("StaticAccess")
	 */
	public function withMethod(string $method):self {
		$method = RequestMethod::filterMethodName($method);
		$clone = clone $this;
		$clone->method = $method;
		return $clone;
	}

	/** @inheritDoc */
	public function getUri():UriInterface {
		return $this->uri;
	}

	/** @inheritDoc */
	public function withUri(UriInterface $uri, bool$preserveHost = false):self {
		$clone = clone $this;

		$host = $uri->getHost();
		if(!empty($host)) {
			if(!$preserveHost
			|| !$this->headers->contains("Host")) {
				$this->headers->add("Host", $host);
			}
		}

		$clone->uri = $uri;
		return $clone;
	}
}
