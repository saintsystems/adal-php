<?php

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