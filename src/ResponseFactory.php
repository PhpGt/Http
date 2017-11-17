<?php
namespace Gt\Http;

use Negotiation\BaseAccept;
use Negotiation\Negotiator;
use Psr\Http\Message\RequestInterface;

class ResponseFactory {
	const DEFAULT_ACCEPT = "text/html";
	protected static $responseClassLookup = [];
	/** @var BaseAccept|null */
	protected static $mediaType;

	/**
	 * A Response object is a PSR-7 compatible object that is created here from the current
	 * Request. The type of Response that is returned is determined by the type of request.
	 * @see http://www.php-fig.org/psr/psr-7
	 */
	public static function create(RequestInterface $request):Response {
		$negotiator = new Negotiator();
		$acceptHeader = $request->getHeaderLine("accept");
		$priorities = [
			"text/html; charset=UTF-8",
			"application/json",
			"application/xml;q=0.5",
		];

		static::$mediaType = $negotiator->getBest(
			$acceptHeader,
			$priorities
		);

		$accept = self::DEFAULT_ACCEPT;
		if(static::$mediaType) {
			$accept = static::$mediaType->getType();
		}

		return new static::$responseClassLookup[$accept];
	}

	/**
	 * In order for this factory to create the correct type of Response, class names need
	 * to be registered to match the request's "accept" header. This allows the web client to
	 * request a web page in HTML format, over a web API in JSON format, for example.
	 */
	public static function registerResponseClass(
		string $responseClassName,
		string...$accept
	):void {
		if(empty($accept)) {
			$accept = [self::DEFAULT_ACCEPT];
		}

		foreach($accept as $a) {
			static::$responseClassLookup [$a] = $responseClassName;
		}
	}
}