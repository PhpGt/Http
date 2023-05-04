<?php
namespace Gt\Http;

use Countable;
use Generator;
use Gt\PropFunc\MagicProp;
use Gt\TypeSafeGetter\NullableTypeSafeGetter;
use Stringable;

/**
 * @property-read int $size The read-only URLSearchParams.size property
 * indicates the total number of search parameter entries.
 */
class URLSearchParams implements Stringable, Countable {
	use MagicProp;
	use NullableTypeSafeGetter;

	/** @var array<string, string|array<string>> */
	private array $kvp;

	/**
	 * @param string|array<string, string>|FormData $options
	 * Options is one of:
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
		string|array|FormData $options = ""
	) {
		if(is_string($options)) {
			$options = trim($options, "?");
			parse_str($options, $query);
			/** @var array<string, string> $query */
			$this->kvp = $query;
		}
		elseif($options instanceof FormData) {

		}
		else {
			$this->kvp = $options;
		}
	}

	/**
	 * @return string A string, without the question mark. Returns an
	 * empty string if no search parameters have been set.
	 */
	public function __toString():string {
		$string = http_build_query($this->kvp);
		return preg_replace(
			"/%5B(\d)%5D/",
			"[]",
			$string
		);
	}

	public function count():int {
		return count($this->kvp);
	}

	public function __prop_get_size():int {
		return count($this);
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
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/append
	 */
	public function append(string $name, string $value):void {
		if(is_array($this->kvp[$name])) {
			array_push($this->kvp[$name], $value);
		}
		else {
			if(isset($this->kvp[$name])) {
				$this->kvp[$name] = [
					$this->kvp[$name],
					$value,
				];
			}
			else {
				$this->set($name, $value);
			}
		}
	}

	/**
	 * The delete() method of the URLSearchParams interface deletes the
	 * given search parameter and all its associated values, from the list
	 * of all search parameters.
	 *
	 * @param string $name The name of the parameter to be deleted.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/delete
	 */
	public function delete(string $name):void {
		$name = rtrim($name, "[]");
		if(!isset($this->kvp[$name])) {
			return;
		}

		unset($this->kvp[$name]);
	}

	/**
	 * The entries() method of the URLSearchParams interface returns an
	 * iterator allowing iteration through all key/value pairs contained
	 * in this object. The iterator returns key/value pairs in the same
	 * order as they appear in the query string. The key and value of each
	 * pair are string objects.
	 *
	 * @return Generator<string, string>
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/entries
	 */
	public function entries():Generator {
		foreach($this->kvp as $key => $value) {
			if(is_array($value)) {
				foreach($value as $subValue) {
					yield "{$key}[]" => $subValue;
				}
			}
			else {
				yield $key => $value;
			}
		}
	}

	/**
	 * The forEach() method of the URLSearchParams interface allows
	 * iteration through all values contained in this object via a
	 * callback function.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/forEach
	 */
	public function forEach(callable $callback):void {
		foreach($this->kvp as $key => $value) {
			if(is_array($value)) {
				foreach($value as $subValue) {
					call_user_func(
						$callback,
						"{$key}[]",
						$subValue
					);
				}
			}
			else {
				call_user_func($callback, $key, $value);
			}
		}
	}

	/**
	 * The get() method of the URLSearchParams interface returns the first
	 * value associated to the given search parameter.
	 *
	 * Note: This class implements type-safe getters, getInt, getBool, etc.
	 *
	 * @param string $name The name of the parameter to return.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/get
	 */
	public function get(string $name):mixed {
		$name = rtrim($name, "[]");
		$value = $this->kvp[$name] ?? null;
		if(!$value) {
			return null;
		}

		if(is_array($value)) {
			return $value[0];
		}

		return $value;
	}

	/**
	 * The getAll() method of the URLSearchParams interface returns all
	 * the values associated with a given search parameter as an array.
	 *
	 * @param string $name The name of the parameter to return.
	 * @return array<string>
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/getAll
	 */
	public function getAll(string $name):array {
		$name = rtrim($name, "[]");
		$value = $this->kvp[$name] ?? [];
		if(!is_array($value)) {
			$value = [$value];
		}

		return $value;
	}

	/**
	 * The has() method of the URLSearchParams interface returns a boolean
	 * value that indicates whether a parameter with the specified name
	 * exists.
	 *
	 * @param string $name The name of the parameter to find.
	 * @return bool
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/has
	 */
	public function has(string $name):bool {
		$name = rtrim($name, "[]");
		return isset($this->kvp[$name]);
	}

	/**
	 * The keys() method of the URLSearchParams interface returns an
	 * iterator allowing iteration through all keys contained in this
	 * object. The keys are string objects.
	 *
	 * @return array<string>
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/keys
	 */
	public function keys():array {
		$keys = [];
		foreach($this->kvp as $key => $value) {
			if(is_array($value)) {
				array_push($keys, "{$key}[]");
			}
			else {
				array_push($keys, $key);
			}
		}

		return $keys;
	}

	/**
	 * The set() method of the URLSearchParams interface sets the value
	 * associated with a given search parameter to the given value. If
	 * there were several matching values, this method deletes the others.
	 * If the search parameter doesn't exist, this method creates it.
	 *
	 * @param string $name The name of the parameter to set.
	 * @param string $value The value of the parameter to set.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/set
	 */
	public function set(string $name, string $value):void {
		$this->kvp[$name] = $value;
	}

	/**
	 * The URLSearchParams.sort() method sorts all key/value pairs
	 * contained in this object in place and returns undefined. The sort
	 * order is according to unicode code points of the keys. This method
	 * uses a stable sorting algorithm (i.e. the relative order between
	 * key/value pairs with equal keys will be preserved).
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/sort
	 */
	public function sort():void {
		ksort($this->kvp);
	}

	/**
	 * The values() method of the URLsearchParams interface returns an
	 * iterator allowing iteration through all values contained in this
	 * object. The values are string objects.
	 *
	 * @return array<string>
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/values
	 */
	public function values():array {
		$values = [];
		foreach($this->kvp as $value) {
			if(is_array($value)) {
				foreach($value as $subValue) {
					array_push($values, $subValue);
				}
			}
			else {
				array_push($values, $value);
			}
		}
		return $values;
	}
}
