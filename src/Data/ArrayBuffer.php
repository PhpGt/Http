<?php
namespace Gt\Http\Data;

use Gt\Http\Getter;
use SplFixedArray;

/**
 * @property-read int $byteLength The read-only size, in bytes, of the ArrayBuffer. This is established when the array is constructed and cannot be changed.
 */
class ArrayBuffer extends SplFixedArray {
	use Getter;

	/**
	 * Returns a new ArrayBuffer whose contents are a copy of this
	 * ArrayBuffer's bytes from begin (inclusive) up to end (exclusive).
	 * If either begin or end is negative, it refers to an index from the
	 * end of the array, as opposed to from the beginning.
	 */
	public function slice(int $begin, int $end = null):ArrayBuffer {
		$begin = $begin >= 0 ? $begin : $this->byteLength - $begin;
		if(is_null($end)) {
			$end = $this->byteLength;
		}
		else {
			$end = $end >= 0 ? $end : $this->byteLength - $end;
		}

		$arrayBuffer = new ArrayBuffer($end - $begin);
		for($i = $begin; $i < $end; $i++) {
			$arrayBuffer[$i] = $this[$i];
		}

		return $arrayBuffer;
	}

	private function prop_get_byteLength():int {
		return $this->getSize();
	}
}