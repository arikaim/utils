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

use DateTimeZone;
use DateInterval;

/**
 * DateTime
 */
class DateTime 
{   
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_TIME_FORMAT = 'H:i';
    const ISO8601_FORMAT      = DATE_ATOM;
    const RFC3339_FORMAT      = DATE_RFC3339;
    const ISO8601ZULU_FORMAT  = 'Y-m-d\TH:i:s\Z';

    /**
     * Time zone
     *
     * @var object
     */
    private static $timeZone;

    /**
     * DateTime object
     *
     * @var \DateTime
     */
    private static $dateTime;

    /**
     * Date formats list
     *
     * @var array
    */
    private static $dateFormats = [];

    /**
     * Time formats list
     *
     * @var array
    */
    private static $timeFormats = [];

    /**
     * Default date format value
     *
     * @var string|null
    */
    private static $defaultDateFormat = null;

    /**
     * Default time format value
     *
     * @var string|null
     */
    private static $defaultTimeFormat = null;

    /**
     * Set date adn time formats
     *
     * @param array $dateFormats
     * @param array $timeFormats
     * @return void
     */
    public static function setFormats(array $dateFormats,array $timeFormats): void
    {
        Self::$dateFormats = $dateFormats;
        Self::$timeFormats = $timeFormats;
    }

    /**
     * Set date formats
     *
     * @param array $dateFormats
     * @param string|null $defultFormat
     * @return void
     */
    public static function setDateFormats(array $dateFormats, ?string $defultFormat = null): void
    {
        Self::$dateFormats = $dateFormats;
        Self::setDefaultDateFormat($defultFormat);
    }

    /**
     * Set time formats
     *
     * @param array $dateFormats
     * @param string|null $defultFormat
     * @return void
     */
    public static function setTimeFormats(array $timeFormats, ?string $defultFormat = null): void
    {
        Self::$timeFormats = $timeFormats;
        Self::setDefaultTimeFormat($defultFormat);
    }

    /**
     * Set default date format
     *
     * @param string|null $format
     * @return void
     */
    public static function setDefaultDateFormat(?string $format): void
    {
        Self::$defaultDateFormat = $format;
    }

    /**
     * Set default time format
     *
     * @param string|null $format
     * @return void
     */
    public static function setDefaultTimeFormat(?string $format): void
    {
        Self::$defaultTimeFormat = $format;
    }

    /**
     * Set date time obj
     *
     * @param string|null $date
     * @param string|null $format
     * @return void
     */
    public static function setDateTime(?string $date = null, ?string $format = null): void
    {        
        Self::$dateTime = Self::create($date,$format);
    }

    /**
     * Create DateTime obj
     *
     * @param string|null $date
     * @param string|null $format
     * @return \DateTime
     */
    public static function create(?string $date = null, ?string $format = null)
    {
        $date = $date ?? 'now';
        $format = Self::getDateFormat($format);

        $dateTime = new \DateTime($date,Self::getTimeZone());
        $dateTime->format($format);

        return $dateTime;
    }

    /**
     * Get DateTime
     *
     * @return \DateTime
     */
    public static function getDateTime()
    {
        if (empty(Self::$dateTime) == true) {
            Self::setDateTime();
        }

        return Self::$dateTime;
    }

    /**
     * Get timestamp
     *
     * @return int
     */
    public static function getTimestamp(): int
    {
        return Self::getDateTime()->getTimestamp();
    }

    /**
     * Get current timestamp
     *
     * @return int
     */
    public static function getCurrentTimestamp(): int
    {
        return Self::create()->getTimestamp();
    } 

    /**
     * Comvert date time to timestamp
     *
     * @param string|null $date
     * @param string|null $format
     * @return integer
     */
    public static function toTimestamp(?string $date = null, ?string $format = null): int
    {
        return Self::create($date,$format)->getTimestamp();    
    }

    /**
     * Get date format
     *
     * @param string|null $name
     * @return string|null
     */
    public static function getDateFormat(?string $name = null): ?string 
    {      
        if (empty($name) == true) {
            return (empty(Self::$defaultDateFormat) == false) ? Self::$defaultDateFormat : Self::DEFAULT_DATE_FORMAT;
        }

        return Self::$dateFormats[$name] ?? $name;
    }

    /**
    * Get time zone list
    *
    * @return array|false
    */
    public static function getTimeZonesList()
    {
        return \timezone_identifiers_list();
    }

    /**
    * Get location
    *
    * @return mixed
    */
    public static function getLocation() 
    {
        return Self::getTimeZone()->getLocation();
    }

    /**
    * Get time zone offset
    *
    * @param  DateTime|null $dateTime
    * @return string
    */
    public static function getTimeZoneOffset($dateTime = null): string 
    {
        return Self::getTimeZone()->getOffset($dateTime ?? Self::$dateTime);
    }

    /**
    * Get time zone
    *
    * @return string
    */
    public static function getTimeZoneName(): string 
    {
        return Self::getTimeZone()->getName();
    }

    /**
     * Get time zone
     *
     * @return DateTimeZone
     */
    public static function getTimeZone()
    {
        if (empty(Self::$timeZone) == true) {
            Self::setTimeZone();
        }

        return Self::$timeZone;
    }

    /**
     * Return true if time zone is vlaid
     *
     * @param string $name
     * @return boolean
     */
    public static function isValidTimeZone(string $name): bool 
    {
        return \in_array($name,\timezone_identifiers_list());
    }

    /**
     * Set time zone
     *
     * @param string|null $name    
     * @return void
     */
    public static function setTimeZone(?string $name = null): void
    {
        $name = (empty($name) == true) ? \date_default_timezone_get() : $name;
        if (Self::isValidTimeZone($name) == false) {
            $name = \date_default_timezone_get();
        }
        Self::$timeZone = new DateTimeZone($name);
    }

    /**
     * Return formated timestsamp with current date and time format
     *
     * @param integer $timestamp
     * @param string|null $format
     * @return string|null
     */
    public static function dateTimeFormat(int $timestamp, ?string $format = null): ?string
    {
        if (\is_numeric($timestamp) == false) {
            return $timestamp;
        }
        if ($format == null) {           
            $format = Self::getDateFormat() . ' ' . Self::getTimeFormat();
        }
        $date = Self::setTimestamp($timestamp);

        return $date->format($format);     
    }

    /**
     * Return formated time
     *
     * @param integer|null $timestamp
     * @param string $format
     * @return string|null
     */
    public static function timeFormat(?int $timestamp, ?string $format = null): ?string
    {
        if (\is_numeric($timestamp) == false) {
            return $timestamp;
        }
        $date = Self::setTimestamp($timestamp);

        return $date->format(Self::getTimeFormat($format)); 
    }

    /**
     * Return formated date
     *
     * @param integer|null $timestamp
     * @param string|null $format
     * @return string|null
     */
    public static function dateFormat(?int $timestamp, ?string $format = null): ?string
    {
        if (\is_numeric($timestamp) == false) {
            return $timestamp;
        }      
        $date = Self::setTimestamp((integer)$timestamp);

        return $date->format(Self::getDateFormat($format));
    }

    /**
     * Get time format
     *
     * @param string|null $name
     * @return string|null
     */
    public static function getTimeFormat(?string $name = null): ?string 
    {       
        if ($name == null) {
            return Self::$defaultTimeFormat ?? Self::DEFAULT_TIME_FORMAT;    
        }

        return Self::$timeFormats[$name] ?? $name; 
    }

    /**
     * Get interval details
     *
     * @param string $intervalText
     * @return array
     */
    public static function getInterval(string $intervalText): array
    {
        $interval = new TimeInterval($intervalText);

        return $interval->toArray();
    }
 
    /**
     * Set date format.
     *
     * @param string|null $dateFormat
     * @return \DateTime
     */
    public static function setDateFormat(?string $dateFormat) 
    {
        Self::$dateTime = Self::getDateTime()->format($dateFormat);

        return Self::$dateTime;
    }

    /**
     * Modify date time
     *
     * @param string|null $dateText
     * @return \DateTime
     */
    public static function modify(?string $dateText) 
    {
        Self::$dateTime = Self::getDateTime()->modify($dateText);

        return Self::$dateTime;
    }

    /**
     * Add interval
     *
     * @param string $dateInterval
     * @return \DateTime
     */
    public static function addInterval(string $dateInterval)
    {
        $interval = DateInterval::createFromDateString($dateInterval); 

        return Self::getDateTime()->add($interval); 
    }
    
    /**
     * Sub interval
     *
     * @param string $dateInterval
     * @return \DateTime
     */
    public static function subInterval(string $dateInterval)
    {
        $interval = DateInterval::createFromDateString($dateInterval); 

        return Self::getDateTime()->sub($interval);         
    }

    /**
     * Set timestamp
     *
     * @param integer $unixTimestamp
     * @return \DateTime
     */
    public static function setTimestamp(int $unixTimestamp) 
    {
        Self::$dateTime = Self::getDateTime()->setTimestamp($unixTimestamp);

        return Self::$dateTime;
    }

    /**
     * Get curent year
     *
     * @return string
     */
    public static function getYear(): string
    {
        return \date('Y',Self::toTimestamp());
    }

    /**
     * Get current year
     *
     * @return string
     */
    public static function getCurrentYear(): string
    {
        return \date('Y');
    }
   
    /**
     * Get last day of month
     *
     * @param integer|string $month
     * @return integer
     */
    public static function getLastDay($month): string
    {
        $date = Self::getYear() . '-' . $month . '-01';
        
        return \date('t',Self::toTimestamp($date,'Y-m-d'));
    }

    /**
     * Get current month
     *
     * @return string
     */
    public static function getMonth(): string
    {
        return \date('n',Self::getTimestamp());
    }
    
    /**
     * Return current day
     *
     * @return string
     */
    public static function getDay(): string
    {
        return \date('j',Self::getTimestamp());
    }

    /**
     * Return current hour
     *
     * @return string
     */
    public static function getHour(): string
    {
        return \date('G',Self::getTimestamp());
    }

    /**
     * Get current minutes
     *
     * @return string
     */
    public static function getMinutes(): string
    {
        return \intval(\date('i',Self::getTimestamp()));
    }

    /**
     * Convert current date time to string.
     *
     * @param string|null $format
     * @return string
     */
    public static function toString(?string $format = null): string 
    {
        return Self::getDateTime()->format(Self::getDateFormat($format));
    }   

    /**
     * Get current date
     *
     * @param string|null $format
     * @return \DateTime
     */
    public static function today(?string $format = null)
    {
        return Self::create('now',$format);
    }

    /**
     * Convert date string to other format
     *
     * @param string $date
     * @param string|null $format
     * @return string|false
     */
    public static function convert($date, ?string $format = null)
    {
        if (\strtotime($date) === false) {
            return false;
        }
        $dateTime = Self::create($date);

        return $dateTime->format(Self::getDateFormat($format));
    }
}
