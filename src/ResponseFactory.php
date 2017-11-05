<?php
namespace Gt\Http;

use Psr\Http\Message\RequestInterface;

class ResponseFactory {
	public static function create(RequestInterface $request):Response {
		return new Response();
	}
}