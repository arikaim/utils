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
     */
    const DEFAULT_FORMAT = [      
        'decimals'            => 2,
        'decimals_separator'  => '.',
        'thousands_separator' => ','
    ]; 

    /**
     *  Text values which may convert to boolean
    */
    const BOOLEAN_TEXT_VALUES = ['true','false','0','1','on','off','yes','no'];

    /**
     * Number format
     *
     * @var array
     */
    private static $format = null;

    /**
     * Format number
     *
     * @param integer|float $number
     * @param string|null|array $formatName
     * @return integer|float
     */
    public static function format($number, $formatName = null)
    {
        $format = Self::resolveFormat($formatName);

        return \number_format($number,$format['decimals'],$format['decimals_separator'],$format['thousands_separator']);
    }

    /**
     * Resolve format
     *
     * @param string|array|null $format
     * @return array
     */
    public static function resolveFormat($format)
    {
        if (\is_array($format) == true) {
            return [
                'decimals'            => $format[0] ?? 2,
                'decimals_separator'  => $format[1] ?? '.',
                'thousands_separator' => $format[2] ?? ','
            ];
        }

        return Self::getFormat($format);       
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
            Self::setFormat($default);
        }
    }

    /**
     * Set number format
     *
     * @param mixed $format
     * @return void
     */
    public static function setFormat($format)
    {      
        Self::$format = Self::resolveFormat($format);
    }

    /**
     * Get format options
     *
     * @param string|null $name
     * @return array
     */
    public static function getFormat($name = null)
    {
        if (empty($name) == true) {            
            return (\is_null(Self::$format) == true) ? Self::DEFAULT_FORMAT : Self::$format;
        } 

        return (\is_array(Self::$formats[$name]) == true) ? Self::$formats[$name] : Self::DEFAULT_FORMAT;          
    }

    /**
     * Return true if variable is number
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isNumber($variable)
    {
        return \is_numeric($variable);
    }

    /**
     * Return true if variable is float
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isFloat($variable)
    {
        return \is_float($variable);
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
        return \intval($value);
    }

    /**
     * Get number fraction
     *
     * @param mixed $value
     * @return float
     */
    public static function getFraction($value)
    {
        return ($value - \intval($value));
    }

    /**
     * Return true if text is boolean value
     *
     * @param string $text
     * @return boolean
     */
    public static function isBoolean($text)
    {       
        $result = \filter_var($text,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);

        return !\is_null($result);
    }

    /**
     * Convert text to bool value
     *
     * @param string $value
     * @return bool
     */
    public static function toBoolean($text)
    {
        $result = \filter_var($text,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);

        return (\is_null($result) == true) ? false : (bool)$result;
    }

    /**
     * Type cast to int, foat, bool
     *
     * @param mixed $number
     * @return mixed
     */
    public static function toNumber($number)
    {
        if (\is_integer($number) == true) {
            return (int)$number;
        }
        if (\is_float($number) == true) {
            return (float)$number;
        }
        if (\is_numeric($number) == true) {
            return (float)$number;
        }

        if (Self::isBoolean($number) == true) {
            return Self::toBoolean($number);
        }

        return (string)$number;
    }
}
