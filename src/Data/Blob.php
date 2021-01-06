<?php
namespace Gt\Http\Data;

use Gt\Http\Getter;
use Gt\Http\Stream;
use Gt\Promise\Deferred;
use Gt\Promise\Promise;
use Stringable;

/**
 * @property-read string $type A string indicating the MIME type of the data contained in the Blob. If the type is unknown, this string is empty.
 */
class Blob {
	use Getter;

	private string $data;
	private string $internalType;
	private string $endings;

	/** @param ArrayBuffer[]|Blob[]|Stringable[] $data */
	public function __construct(array $data, array $options = []) {
		if(isset($options["type"])) {
			$this->internalType = $options["type"];
		}
		if(isset($options["endings"])) {
			$this->endings = $options["endings"];
		}

		$this->data = "";
		foreach($data as $datum) {
			if(isset($this->endings)) {
				$datum = str_replace(
					["\n", "\r\n"],
					$this->endings,
					$datum
				);
			}

			$this->data .= $datum;
		}
	}

	public function prop_get_type():string {
		return $this->internalType ?? "";
	}

	/**
	 * Returns a promise that resolves with an ArrayBuffer containing the
	 * entire contents of the Blob as binary data.
	 */
	public function arrayBuffer():Promise {
		$size = strlen($this->data);
		$arrayBuffer = new ArrayBuffer($size);

		for($i = 0; $i < $size; $i++) {
			$arrayBuffer[$i] = $this->data[$i];
		}

		$deferred = new Deferred();
		$promise = $deferred->getPromise();
		$deferred->resolve($arrayBuffer);
		return $promise;
	}

	/**
	 * Returns a new Blob object containing the data in the specified range
	 * of bytes of the blob on which it's called.
	 */
	public function slice(
		int $start = 0,
		int $end = null,
		string $contentType = null
	):Promise {
		$options = [];
		if($contentType) {
			$options["type"] = $contentType;
		}
		if(is_null($end)) {
			$end = strlen($this->data);
		}
		$length = $end - $start;
		$data = substr($this->data, $start, $length);

		$blob = new Blob([$data], $options);
		$deferred = new Deferred();
		$promise = $deferred->getPromise();
		$deferred->resolve($blob);
		return $promise;
	}

	/**
	 * Returns a Stream that can be used to read the contents of the Blob.
	 */
	public function stream():Stream {
		$stream = new Stream();
		$stream->write($this->data);
		$stream->rewind();
		return $stream;
	}

	/**
	 * Returns a promise that resolves with a string containing the
	 * entire contents of the Blob.
	 */
	public function text():Promise {
		$deferred = new Deferred();
		$promise = $deferred->getPromise();
		$deferred->resolve($this->data);
		return $promise;
	}
}