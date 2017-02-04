<?php

if (! function_exists('encodeURIComponent')) {
	/**
	 * PHP equivalent of JavaScript encodeURIComponent
	 * Credit: http://stackoverflow.com/questions/1734250/what-is-the-equivalent-of-javascripts-encodeuricomponent-in-php
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	function encodeURIComponent($str) {
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($str), $revert);
	}
}

if (! function_exists('starts_with')) {
	/**
	 * Check if a string starts with the given string.
	 *
	 * @param  string $string
	 * @param  string $starts_with
	 * @return boolean
	 */
	function starts_with($string, $starts_with)
	{
	    return (strpos($string, $starts_with) === 0);
	}
}

if (! function_exists('ends_with')) {
	/**
	 * Check if a string ends with the given string.
	 *
	 * @param  string $string
	 * @param  string $ends_with
	 * @return boolean
	 */
	function ends_with($string, $ends_with)
	{
	    return substr($string, -strlen($ends_with)) === $ends_with;
	}
}

if (! function_exists('str_contains')) {
	/**
	 * Check if a string contains another string.
	 *
	 * @param  string $haystack
	 * @param  string $needle
	 * @return boolean
	 */
	function str_contains($haystack, $needle)
	{
	    return (strpos($haystack, $needle) !== false);
	}
}

if (! function_exists('str_icontains')) {
	/**
	 * Check if a string contains another string. This version is case
	 * insensitive.
	 *
	 * @param  string $haystack
	 * @param  string $needle
	 * @return boolean
	 */
	function str_icontains($haystack, $needle)
	{
	    return (stripos($haystack, $needle) !== false);
	}
}
