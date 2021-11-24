<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

use DateInterval;
use DateTime;

/**
 * Time intervals
 */
class TimeInterval
{
    const TYPE_ISO_8601 = 0;
    const TYPE_CRONTAB  = 1;
    
    /**
     * Interval obj
     *
     * @var object
     */
    private static $interval = null;

    /**
     * Create interval
     *
     * @param mixed $interval
     * @return DateInterval|null
     */
    public static function create($interval = '')
    {
        if (Self::isDurationInverval($interval) == true) {
            return new DateInterval($interval);
        }
        if (\strtotime($interval) !== false) {
            return DateInterval::createFromDateString($interval);
        }
        if (\is_numeric($interval) == true) {
            return Self::createFromSeconds($interval);
        }

        return null;  
    } 

    /**
     * Create time interval from soconds
     *
     * @param integer $seconds
     * @return DateInterval
     */
    public static function createFromSeconds(int $seconds) 
    {
        $start = new DateTime('@0');
        $end = new DateTime('@' . (string)$seconds);

        return $start->diff($end);
    }

    /**
     * Return interval object
     *
     * @param string $interval
     * @return object
     */
    public static function getDateInterval($interval = '')
    {
        if (empty(Self::$interval) == true) {
            Self::$interval = Self::create($interval);
        }

        return Self::$interval;
    }

    /**
     * Get years
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getYears($interval = null)
    {
        return Self::getDateInterval($interval)->y;
    }

    /**
     * Get months
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getMonths($interval = null)
    {
        return Self::getDateInterval($interval)->m;
    }

    /**
     * Get hours
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getHours($interval = null)
    {
        return Self::getDateInterval($interval)->h;
    }

    /**
     * Get minutes
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getMinutes($interval = null)
    {
        return Self::getDateInterval($interval)->i;
    }

    /**
     * Get days
     *
     * @param string|null $interval
     * @return integer
     */
    public static function getDays($interval = null)
    {
        return Self::getDateInterval($interval)->d;
    }

    /**
     * Get date time interval as string
     *
     * @return string
     */
    public static function getInterval()
    {
        $years   = (Self::getYears() > 0) ? Self::getYears() . 'Y' : '';
        $months  = (Self::getMonths() > 0) ? Self::getMonths() . 'M' : '';
        $days    = (Self::getDays() > 0) ? Self::getDays() . 'D' : '';
        $hours   = (Self::getHours() > 0) ? Self::getHours() . 'H' : '';
        $minutes = (Self::getMinutes() > 0) ? Self::getMinutes() . 'M' : '';

        return 'P' . $years . $months . $days . 'T' . $hours . $minutes;        
    }

    /**
     * Set years
     *
     * @param integer $years
     * @return void
     */
    public static function setYears($years)
    {
        Self::$interval = Self::getDateInterval()->y = $years;
    }

    /**
     * Set months
     *
     * @param integer $months
     * @return void
     */
    public static function setMonths($months)
    {
        Self::$interval = Self::getDateInterval()->m = $months;
    }

    /**
     * Set days
     *
     * @param integer $days
     * @return void
     */
    public static function setDays($days): void
    {
        Self::$interval = Self::getDateInterval()->d = $days;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     * @return void
     */
    public static function setHours($hours): void
    {
        Self::$interval = Self::getDateInterval()->h = $hours;
    }

    /**
     * Set minutes
     *
     * @param integer $minutes
     * @return void
     */
    public static function setMinutes($minutes): void
    {
        Self::$interval = Self::getDateInterval()->i = $minutes;
    }

    /**
     * Convert interval to array
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [
            'years'     => Self::getYears(),
            'months'    => Self::getMonths(),
            'days'      => Self::getDays(),
            'hours'     => Self::getHours(),
            'minutes'   => Self::getMinutes()
        ];
    }

    /**
     * Return true if text is valid interval string
     *
     * @param string $text
     * @return boolean
     */
    public static function isDurationInverval(string $text): bool
    {
        return (\substr($text,0,1) == 'P');   
    }
}
