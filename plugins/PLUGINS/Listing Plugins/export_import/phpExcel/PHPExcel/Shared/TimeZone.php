<?php
class PHPExcel_Shared_TimeZone
{
    protected static $_timezone = 'UTC';
    public static function _validateTimeZone($timezone)
    {
        if (in_array($timezone, DateTimeZone::listIdentifiers())) {
            return TRUE;
        }
        return FALSE;
    }
    public static function setTimeZone($timezone)
    {
        if (self::_validateTimezone($timezone)) {
            self::$_timezone = $timezone;
            return TRUE;
        }
        return FALSE;
    }
    public static function getTimeZone()
    {
        return self::$_timezone;
    }
    private static function _getTimezoneTransitions($objTimezone, $timestamp)
    {
        $allTransitions = $objTimezone->getTransitions();
        $transitions    = array();
        foreach ($allTransitions as $key => $transition) {
            if ($transition['ts'] > $timestamp) {
                $transitions[] = ($key > 0) ? $allTransitions[$key - 1] : $transition;
                break;
            }
            if (empty($transitions)) {
                $transitions[] = end($allTransitions);
            }
        }
        return $transitions;
    }
    public static function getTimeZoneAdjustment($timezone, $timestamp)
    {
        if ($timezone !== NULL) {
            if (!self::_validateTimezone($timezone)) {
                throw new PHPExcel_Exception("Invalid timezone " . $timezone);
            }
        } else {
            $timezone = self::$_timezone;
        }
        if ($timezone == 'UST') {
            return 0;
        }
        $objTimezone = new DateTimeZone($timezone);
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $transitions = $objTimezone->getTransitions($timestamp, $timestamp);
        } else {
            $transitions = self::_getTimezoneTransitions($objTimezone, $timestamp);
        }
        return (count($transitions) > 0) ? $transitions[0]['offset'] : 0;
    }
}