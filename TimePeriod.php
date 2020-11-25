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

use Arikaim\Core\Utils\DateTime;

/**
 * Time periods
 */
class TimePeriod
{
    /**
     * Check if timestamp is today
     *        
     * @param int $timestamp
     * @return bool
    */
    public static function isToday($timestamp)
    {
        $period = Self::getDayPeriod();

        return ($timestamp >= $period['start'] && $timestamp >= $period['end']);
    }

    /**
     * Check if timestamp is in year 
     * 
     * @param int $timestamp          
     * @param int|null $year
     * @return bool
    */
    public static function isYear($timestamp, $year = null)
    {
        $period = Self::getYearPeriod($year);

        return ($timestamp >= $period['start'] && $timestamp >= $period['end']);
    }


    /**
     * Check if timestamp is in month 
     * 
     * @param int $timestamp       
     * @param int|null $month
     * @param int|null $year
     * @return bool
    */
    public static function isMonth($timestamp, $month = null, $year = null)
    {
        $period = Self::getMonthPeriod($month,$year);

        return ($timestamp >= $period['start'] && $timestamp >= $period['end']);
    }

    /**
     * Get month period
     *        
     * @param int|null $year
     * @return array
    */
    public static function getYearPeriod($year = null)
    {
        $year = $year ?? \date('Y');

        return Self::getPeriod($year . '-01-01T00:00:00.0',$year . '-12-31T12:59:59.0'); 
    } 

    /**
     * Get month period
     *   
     * @param int|null $month
     * @param int|null $year
     * @return array
     */
    public static function getMonthPeriod($month = null, $year = null)
    {
        $year = (empty($year) == true) ? \date('Y') : $year;
        $month = (empty($month) == true) ? \date('m') : $month;
        $lastDay = DateTime::getLastDay($month);
        $startDate = $year . '-' . $month . '-01T00:00:00.0';
        $endDate = $year . '-' . $month . '-' . $lastDay . 'T12:59:59.0';

        return Self::getPeriod($startDate,$endDate);       
    }

    /**
     * Get day period
     *
     * @param int|null $day
     * @param int|null $month
     * @param int|null $year
     * @return array
     */
    public static function getDayPeriod($day = null, $month = null, $year = null)
    {
        $day = (empty($day) == true) ? \date('j') : $day;
        $year = (empty($year) == true) ? \date('Y') : $year;
        $month = (empty($month) == true) ? \date('m') : $month;
        $startDate = $year . '-' . $month . '-' . $day . 'T00:00:00.0';
        $endDate = $year . '-' . $month . '-' . $day . 'T12:59:59.0';

        return Self::getPeriod($startDate,$endDate);      
    }

    /**
     * Get period
     *
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public static function getPeriod($fromDate, $toDate)
    {
        return [
            'start' => DateTime::toTimestamp($fromDate),
            'end'   => DateTime::toTimestamp($toDate)
        ];
    }
}
