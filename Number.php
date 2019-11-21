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

/**
 * Number helper
 */
class Number 
{   
    /**
     * Number formats
     *
     * @var array
     */
    protected static $formats = [];

    /**
     * Default format values
     *
     * @var array
     */
    protected static $defaultFormat = [
        'title'               => 'Default',
        'decimals'            => 2,
        'decimals_separator'  => ",",
        'thousands_separator' => " "
    ]; 

    /**
     * Format number
     *
     * @param integer|float $number
     * @param string|null|array $formatName
     * @return integer|float
     */
    public static function format($number, $formatName = null)
    {
        $format = (is_array($formatName) == false) ? Self::getFormat($formatName) : $formatName;
           
        return number_format($number,$format['decimals'],$format['decimals_separator'],$format['thousands_separator']);
    }

    /**
     * Set formats list
     *
     * @param array $items
     * @param array|null $default
     * @return void
     */
    public static function setFormats(array $items, $default = null)
    {
        Self::$formats = $items;

        if (empty($default) == false) {
            Self::$defaultFormat = $default;
        }

    }

    /**
     * Get format options
     *
     * @param string $name
     * @return array
     */
    public static function getFormat($name = null)
    {
        if ($name == null) {
            return Self::$defaultFormat;
        } 

        return (isset(Self::$formats[$name]) == true) ? Self::$formats[$name] : Self::$defaultFormat;          
    }

    /**
     * Return true if variable is number
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isNumber($variable)
    {
        return is_numeric($variable);
    }

    /**
     * Return true if variable is float
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isFloat($variable)
    {
        return is_float($variable);
    }

    /**
     * Return 0 if variable is not number
     *
     * @param mixed $value
     * @return integer|float
     */
    public static function getNumericValue($value) 
    {
        return (Self::isNumber($value) == false) ? 0 : $value;
    }

    /**
     * Get integer value
     *
     * @param mixed $value
     * @return integer
     */
    public static function getInteger($value)
    {
        return intval($value);
    }

    /**
     * Get number fraction
     *
     * @param mixed $value
     * @return float
     */
    public static function getFraction($value)
    {
        return ($value - intval($value));
    }
}
