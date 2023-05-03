<?php
namespace Gt\Http;

use Gt\PropFunc\MagicProp;
use Gt\TypeSafeGetter\NullableTypeSafeGetter;
use Stringable;

/**
 * @property-read int $size The read-only URLSearchParams.size property
 * indicates the total number of search parameter entries.
 */
class URLSearchParams implements Stringable {
	use MagicProp;
	use NullableTypeSafeGetter;

	/**
	 * @param string|array|object|FormData $options Options is one of:
	 * 1) A string, which will be parsed from
	 * application/x-www-form-urlencoded format. A leading '?' character
	 * is ignored.
	 * 2) A literal sequence of name-value string pairs, or any object —
	 * such as a FormData object — with an iterator that produces a
	 * sequence of string pairs. Note that File entries will be
	 * serialized as [object File] rather than as their filename (as they
	 * would in an application/x-www-form-urlencoded form).
	 * 3) A record of string keys and string values. Note that nesting is
	 * not supported.
	 */
	public function __construct(
		private string|array|FormData $options
	) {}

	/**
	 * @return string A string, without the question mark. Returns an
	 * empty string if no search parameters have been set.
	 */
	public function __toString():string {

	}

	public function __get_size():int {

	}

	/**
	 * The append() method of the URLSearchParams interface appends a
	 * specified key/value pair as a new search parameter.
	 *
	 * If the same key is appended multiple times it will appear in the
	 * parameter string multiple times for each value.
	 *
	 * @param string $name The name of the parameter to append.
	 * @param string $value The value of the parameter to append.
	 */
	public function append(string $name, string $value):void {

	}

	/**
	 * The delete() method of the URLSearchParams interface deletes the
	 * given search parameter and all its associated values, from the list
	 * of all search parameters.
	 *
	 * @param string $name The name of the parameter to be deleted.
	 */
	public function delete(string $name):void {

	}

	/**
	 * The entries() method of the URLSearchParams interface returns an
	 * iterator allowing iteration through all key/value pairs contained
	 * in this object. The iterator returns key/value pairs in the same
	 * order as they appear in the query string. The key and value of each
	 * pair are string objects.
	 *
	 * @return array<string>
	 */
	public function entries():array {

	}

	/**
	 * The forEach() method of the URLSearchParams interface allows
	 * iteration through all values contained in this object via a
	 * callback function.
	 */
	public function forEach(callable $callback):void {

	}

	/**
	 * The get() method of the URLSearchParams interface returns the first
	 * value associated to the given search parameter.
	 *
	 * Note: This class implements type-safe getters, getInt, getBool, etc.
	 *
	 * @param string $name The name of the parameter to return.
	 */
	public function get(string $name):mixed {

	}

	/**
	 * The getAll() method of the URLSearchParams interface returns all
	 * the values associated with a given search parameter as an array.
	 *
	 * @param string $name The name of the parameter to return.
	 * @return array<string>
	 */
	public function getAll(string $name):array {

	}

	/**
	 * The has() method of the URLSearchParams interface returns a boolean
	 * value that indicates whether a parameter with the specified name
	 * exists.
	 *
	 * @param string $name The name of the parameter to find.
	 * @return bool
	 */
	public function has(string $name):bool {

	}

	/**
	 * The keys() method of the URLSearchParams interface returns an
	 * iterator allowing iteration through all keys contained in this
	 * object. The keys are string objects.
	 *
	 * @return array<string>
	 */
	public function keys():array {

	}

	/**
	 * The set() method of the URLSearchParams interface sets the value
	 * associated with a given search parameter to the given value. If
	 * there were several matching values, this method deletes the others.
	 * If the search parameter doesn't exist, this method creates it.
	 *
	 * @param string $name The name of the parameter to set.
	 * @param string $value The value of the parameter to set.
	 */
	public function set(string $name, string $value):void {

	}

	/**
	 * The URLSearchParams.sort() method sorts all key/value pairs
	 * contained in this object in place and returns undefined. The sort
	 * order is according to unicode code points of the keys. This method
	 * uses a stable sorting algorithm (i.e. the relative order between
	 * key/value pairs with equal keys will be preserved).
	 */
	public function sort():void {

	}

	/**
	 * The values() method of the URLsearchParams interface returns an
	 * iterator allowing iteration through all values contained in this
	 * object. The values are string objects.
	 *
	 * @return array<string>
	 */
	public function values():array {

	}
}
