<?php

/**
 * Spoon Library - Date Handling Class
 * 
 * Provides additional functionalities for handling dates and time.
 *
 * @package     spoon
 * @subpackage  date
 * @author      Davy Hellemans <davy@spoon-library.com>
 * @since       0.1.1
 */
class SpoonDate
{
    /**
     * Formats a given timestamp into a localized date string.
     *
     * @param string $format Date format.
     * @param int|null $timestamp UNIX timestamp.
     * @param string $language Language code.
     * @param bool $GMT Whether to use GMT/UTC.
     * @return string Formatted date string.
     */
    public static function getDate($format, $timestamp = null, $language = 'en', $GMT = false)
    {
        $timestamp = $timestamp ?? time();
        if (!is_int($timestamp) || $timestamp < 0) {
            throw new SpoonDateException('Invalid timestamp.');
        }
        
        $date = $GMT ? gmdate($format, $timestamp) : date($format, $timestamp);
        
        if ($language !== 'en') {
            $date = self::translateDate($date, $language);
        }
        
        return $date;
    }
    
    /**
     * Converts English weekday and month names to a given language.
     *
     * @param string $date The original date string.
     * @param string $language Language code.
     * @return string Translated date string.
     */
    private static function translateDate($date, $language)
    {
        $weekdays = SpoonLocale::getWeekDays($language);
        $shortWeekdays = SpoonLocale::getWeekDays($language, true);
        $months = SpoonLocale::getMonths($language);
        $shortMonths = SpoonLocale::getMonths($language, true);
        
        $date = str_replace([
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
        ], $weekdays, $date);
        
        $date = str_replace([
            'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
        ], $shortWeekdays, $date);
        
        $date = str_replace([
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        ], $months, $date);
        
        return str_replace([
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ], $shortMonths, $date);
    }
    
    /**
     * Returns a human-readable "time ago" string.
     *
     * @param int $timestamp The past timestamp.
     * @param string $language Language code.
     * @param string|null $format Format for dates older than a week.
     * @return string Human-readable time difference.
     */
    public static function getTimeAgo($timestamp, $language = 'en', $format = null)
    {
        if (!is_int($timestamp) || $timestamp < 0) {
            throw new SpoonDateException('Invalid timestamp.');
        }
        
        $secondsBetween = time() - $timestamp;
        $timeUnits = [
            'Year' => 31556952,
            'Month' => 2629746,
            'Week' => 604800,
            'Day' => 86400,
            'Hour' => 3600,
            'Minute' => 60,
            'Second' => 1
        ];
        
        require 'spoon/locale/data/' . $language . '.php';
        
        foreach ($timeUnits as $unit => $seconds) {
            $count = floor($secondsBetween / $seconds);
            if ($count >= 1) {
                return ($format && $unit === 'Year') ? self::getDate($format, $timestamp, $language) : sprintf($locale['time'][$unit . 'sAgo'], $count);
            }
        }
        
        return $locale['time']['SecondAgo'];
    }
}

/**
 * Exception class for date-related errors.
 */
class SpoonDateException extends SpoonException {}