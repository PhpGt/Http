<?php
namespace Gt\Http;

class ResponseFactory {
	public static function create():Response {
		return new Response();
	}
}