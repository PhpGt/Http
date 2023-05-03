<?php
namespace Gt\Http;

use Gt\Http\Header\ResponseHeaders;
use InvalidArgumentException;
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
		private readonly ?Request $request = null,
	) {
		$this->headers = $headers ?? new ResponseHeaders();
		$this->stream = new Stream();
	}

	public function setExitCallback(callable $callback):void {
		$this->exitCallback = $callback;
	}

	public function reload():void {
		$this->redirect($this->request?->getUri() ?? new Uri());
	}

	public function reloadWithoutQuery():void {
		$uri = $this->request?->getUri() ?? new Uri();
		$uri = $uri->withQuery("");
		$this->redirect($uri);
	}

	public function redirect(
		string|UriInterface $uri,
		int $statusCode = 303
	):void {
		$this->statusCode = $statusCode;
		$this->headers->set("Location", (string)$uri);
		if(isset($this->exitCallback)) {
			call_user_func($this->exitCallback);
		}
	}

	/** @inheritDoc */
	public function getStatusCode():int {
		return $this->statusCode;
	}

	/** @inheritDoc */
// phpcs:ignore
	public function withStatus(
		int $code,
		string $reasonPhrase = ''
	):self {
		$clone = clone $this;
		$clone->statusCode = $code;
		return $clone;
	}

	/** @inheritDoc */
	public function getReasonPhrase():string {
		return StatusCode::REASON_PHRASE[$this->statusCode];
	}

	public function getResponseHeaders():ResponseHeaders {
		return $this->headers;
	}
}
