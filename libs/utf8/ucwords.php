<?php
function utf8_ucwords($str)
{
    $pattern = '/(^|([\x0c\x09\x0b\x0a\x0d\x20]+))([^\x0c\x09\x0b\x0a\x0d\x20]{1})[^\x0c\x09\x0b\x0a\x0d\x20]*/u';
    return preg_replace_callback($pattern, 'utf8_ucwords_callback', $str);
}
function utf8_ucwords_callback($matches)
{
    $leadingws = $matches[2];
    $ucfirst   = utf8_strtoupper($matches[3]);
    $ucword    = utf8_substr_replace(ltrim($matches[0]), $ucfirst, 0, 1);
    return $leadingws . $ucword;
}

