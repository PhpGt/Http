<?php
namespace Gt\Http;

use DOMElement;
use Gt\TypeSafeGetter\NullableTypeSafeGetter;

class FormData {
	use NullableTypeSafeGetter;

	/**
	 * @param ?DOMElement $form An HTML <form> element — when specified,
	 * the FormData object will be populated with the form's current
	 * keys/values using the name property of each element for the keys and
	 * their submitted value for the values. It will also encode file
	 * input content.
	 * @param ?DOMElement|null $submitter A submit button that is a member
	 * of the form. If the submitter has a name attribute or is an
	 * <input type="image">, its data will be included in the FormData
	 * object (e.g. btnName=btnValue).
	 */
	public function __construct(
		private readonly ?DOMElement $form = null,
		private readonly ?DOMElement $submitter = null,
	) {
		if($submitter) {
			$foundForm = false;
			while($parent = $submitter->parentNode) {
				if($parent === $this->form) {
					$foundForm = true;
					break;
				}
			}

			if(!$foundForm) {
				throw new HttpException(
					"Submitter is not part of the form"
				);
			}
		}
	}

	/**
	 * The append() method of the FormData interface appends a new value
	 * onto an existing key inside a FormData object, or adds the key if
	 * it does not already exist.
	 *
	 * The difference between set() and append() is that if the specified
	 * key already exists, set() will overwrite all existing values with
	 * the new one, whereas append() will append the new value onto the
	 * end of the existing set of values.
	 *
	 * @param string $name The name of the field whose data is contained
	 * in value.
	 * @param string $value The field's value. This can be a string or
	 * Blob (including subclasses such as File). If none of these are
	 * specified the value is converted to a string.
	 * @param ?string $filename The filename reported to the server , when
	 * a Blob or File is passed as the second parameter.
	 * The default filename for Blob objects is "blob". The default
	 * filename for File objects is the file's filename.
	 */
	public function append(
		string $name,
		Blob|FileUpload|string $value,
		string $filename = null
	):void {

	}

	/**
	 * The delete() method of the FormData interface deletes a key and its
	 * value(s) from a FormData object.
	 */
	public function delete(string $name):void {

	}

	/**
	 * The FormData.entries() method returns an iterator which iterates
	 * through all key/value pairs contained in the FormData. The key of
	 * each pair is a string object, and the value is either a string
	 * or a Blob.
	 *
	 * @return array<string|Blob|FileUpload>
	 */
	public function entries():array {

	}

	/**
	 * The get() method of the FormData interface returns the first value
	 * associated with a given key from within a FormData object. If you
	 * expect multiple values and want all of them, use the getAll()
	 * method instead.
	 *
	 * This class provides type-safe getters: getInt, getBool, etc.
	 */
	public function get(string $name):mixed {

	}

	/**
	 * The getAll() method of the FormData interface returns all the values associated with a given key from within a FormData object.
	 *
	 * @param string $name A string representing the name of the key you
	 * want to retrieve.
	 *
	 * @return array<string|Blob|FileUpload>
	 */
	public function getAll(string $name):array {

	}

	/**
	 * The has() method of the FormData interface returns whether a
	 * FormData object contains a certain key.
	 *
	 * @param string $name A string representing the name of the key you
	 * want to test for.
	 */
	public function has(string $name):bool {

	}

	/**
	 * The FormData.keys() method returns an iterator which iterates
	 * through all keys contained in the FormData. The keys are strings.
	 *
	 * @return array<string>
	 */
	public function keys():array {

	}

	/**
	 * The set() method of the FormData interface sets a new value for an
	 * existing key inside a FormData object, or adds the key/value if it
	 * does not already exist.
	 *
	 * The difference between set() and append() is that if the specified
	 * key does already exist, set() will overwrite all existing values
	 * with the new one, whereas append() will append the new value onto
	 * the end of the existing set of values.
	 *
	 * @param string $name The name of the field whose data is contained
	 * in value.
	 * @param Blob|FileUpload|string $value The field's value. This can be
	 * a string or Blob (including subclasses such as File). If none of
	 * these are specified the value is converted to a string.
	 * @param ?string $filename The filename reported to the server,
	 * when a Blob or File is passed as the second parameter. The default
	 * filename for Blob objects is "blob". The default filename for File
	 * objects is the file's filename.
	 */
	public function set(
		string $name,
		Blob|FileUpload|string $value,
		?string $filename = null,
	):void {

	}

	/**
	 * The FormData.values() method returns an iterator which iterates
	 * through all values contained in the FormData. The values are
	 * strings or Blob objects.
	 *
	 * @return array<Blob|FileUpload|string>
	 */
	public function values():array {

	}
}
