<?php
/**
 * For helpers that can help work with formats
 */

namespace rizwanjiwan\common\interfaces;


interface FormatHelper
{

	/**
	 * Set a value to use in this formatter
	 * @param $value string
	 */
	public function setValue(string $value);
	/**
	 * Check if the set value is valid for this format
	 * @return boolean true if the value is valid
	 */
	public function isValid(): bool;

	/**
	 * @return string|null human friendly reason why the format is invalid. Null if it is valid or you never checked.
	 */
	public function getInvalidFormatReason():?string;

	/**
	 * @param mixed $format_type null for default format, otherwise, implementations may provide ways of indicating different formats to request
	 * @return string the value formatted appropriately. Will do best effort if isValid() doesn't return true
	 */
	public function getFormatted(?int $format_type=null):string;
}