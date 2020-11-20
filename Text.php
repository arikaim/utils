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
 * text helpers
 */
class Text 
{
    const LOWER_CASE         = 1;
    const UPPER_CASE         = 2;
    const FIRST_LETTER_UPPER = 3;

    /**
     * Replace umlauts chars
     *
     * @param string $text
     * @return string
     */
    public static function replaceChars($text)
    {
        $chars = [
            "ä" => "ae",
            "ö" => "oe",
            "ß" => "ss",
            "ü" => "ue",
            "æ" => "ae",
            "ø" => "oe",
            "å" => "aa",
            "é" => "e",
            "è" => "e",
            "ó" => 'o'
        ];

        return \str_replace(\array_keys($chars),\array_values($chars),$text);
    }

    /**
     * Clean Text
     *
     * @param string $text
     * @return string
     */
    public static function cleanText($text)
    {
        return \preg_replace('/[[:^print:]]/','',$text);
    }

    /**
     * Pad a string to a certain length with another string (both side)
     *
     * @param string $input
     * @param integer $length
     * @param string $char
     * @param boolean $htmlSafe
     * @return string
     */
    public static function pad($input, $length, $char = ' ', $htmlSafe = true)
    {
        $output = \str_pad($input,$length,$char,STR_PAD_BOTH);

        return ($htmlSafe == true) ? \str_replace(' ','&nbsp;',$output) : $output;
    }

    /**
     * Pad left a string to a certain length with another string
     *
     * @param string $input
     * @param integer $length
     * @param string $char
     * @param boolean $htmlSafe
     * @return string
     */
    public static function padLeft($input, $length, $char = ' ', $htmlSafe = true)
    {
        $output = \str_pad($input,$length,$char,STR_PAD_LEFT);

        return ($htmlSafe == true) ? \str_replace(' ','&nbsp;',$output) : $output;
    }

    /**
     * Pad right a string to a certain length with another string
     *
     * @param string $input
     * @param integer $length
     * @param string $char
     * @param boolean $htmlSafe
     * @return string
     */
    public static function padRight($input, $length, $char = ' ', $htmlSafe = true)
    {
        $output = \str_pad($input,$length,$char, STR_PAD_RIGHT);

        return ($htmlSafe == true) ? \str_replace(' ','&nbsp;',$output) : $output;
    }

    /**
     * Mask text
     *
     * @param string $text
     * @param integer $len
     * @param string $maskChar
     * @return string
     */
    public static function mask($text, $len = 5, $maskChar = '*')
    {
        return \str_repeat($maskChar,\strlen($text) - $len) . \substr($text, - $len);           
    }

    /**
     * Upper case first letter for Utf8
     *
     * @param string $text
     * @return string
     */
    public static function ucFirstUtf($text) {      
        return (\function_exists('mb_convert_case') == true ) ? \mb_convert_case($text,MB_CASE_TITLE,'UTF-8') : $text;         
    }

    /**
     * Slice text
     *
     * @param string $text
     * @param integer $maxLength
     * @return string
     */
    public static function sliceText($text, $maxLength = 30)
    {
        if (\strlen($text) > $maxLength) {
            $text = \substr(trim($text),0,$maxLength);    
            $pos = \strrpos($text,' ');
            return ($pos > 0) ? \substr($text,0,$pos) : $text;   
        }
        
        return $text;
    }

    /**
     * Tokenize text split to words
     *
     * @param string|array $text
     * @param mixed ...$options
     * @return array
     */
    public static function tokenize($text, ...$options)
    {
        $delimiter = (isset($options[0]) == true) ? $options[0] : ' ';
        $case = (isset($options[1]) == true) ? $options[1] : null;
        $unique = (isset($options[2]) == true) ? $options[2] : true;

        $tokens = (\is_string($text) == true) ? \explode($delimiter,$text) : $text; 
    
        if ($unique == true) {
            $tokens = \array_unique($tokens);
        }

        foreach ($tokens as $key => $value) {
            if (empty($value) == true) {
                continue;
            }
            $word = Self::transformWord($value,$case);
            if (empty($word) == true) {
                unset($tokens[$key]);
            } else {
                $tokens[$key] = $word;
            }
        }
         
        return $tokens;
    }

    /**
     * Transfor word ( removes all not a-z chars )
     *
     * @param string $word
     * @param mixed  ...$options   1 - case
     * @return void
     */
    public static function transformWord($word, ...$options)
    {       
        $case = (isset($options[0]) == true) ? $options[0] : Text::LOWER_CASE;
        $removeNumbers = (isset($options[1]) == true) ? $options[1] : false;

        $word = Self::removeSpecialChars($word,$removeNumbers);

        switch($case) {
            case Text::LOWER_CASE: 
                $word = \strtolower($word);
                break;
            case Text::UPPER_CASE: 
                $word = \strtoupper($word);
                break;
            case Text::FIRST_LETTER_UPPER:
                $word = \ucfirst($word);
                break;
        }

        return \trim($word);
    }

    /**
     * Remove special chars and numbers from text
     *
     * @param string $text
     * @param boolean $removeNumbers
     * @return string
     */
    public static function removeSpecialChars($text, $removeNumbers = false) 
    {        
        return ($removeNumbers == true) ? \preg_replace('/[^a-zA-Z ]/i','',\trim($text)) : \preg_replace('/[^a-zA-Z0-9]/','',$text);
    }

    /**
     * Convert to title (pascal) case
     *
     * @param string $text
     * @return string
     */
    public static function convertToTitleCase($text)
    {      
        $tokens = \explode('_',$text);
        $result = '';
        foreach ($tokens as $word) {
            $result .= \ucfirst($word);
        }

        return $result;
    }

    /**
     * Replace all code {{ var }} in text with var value
     * 
     * @param string $text
     * @param array $vars
     * @return string
     */
    public static function render($text, $vars = []) 
    {    
        $result = \preg_replace_callback('/\{\{(.*?)\}\}/',function ($matches) use ($vars) {
            $variableName = \trim(\strtolower($matches[1]));
            return (\array_key_exists($variableName,$vars) == true) ? $vars[$variableName] : '';               
        },$text);

        return ($result == null) ? $text : $result;        
    }

    /**
     * Render multiple text items
     *
     * @param array $items
     * @param array $vars
     * @return array
     */
    public static function renderMultiple(array $items, $vars = [])
    {
        foreach ($items as $key => $value) {          
            if (\is_string($value) == true) {
                $items[$key] = Text::render($value,$vars);
            }
        }
        
        return $items;
    }

    /**
     * Ceate random token
     *
     * @param  int $length
     * @return string
     */
    public static function createToken($length = 22)
    {
        $token = '';
        while (($len = \strlen($token)) < $length) {
            $size = $length - $len;
            $bytes = \random_bytes($size);
            $token .= \substr(\str_replace(['/','+','='],'',\base64_encode($bytes)),0,$size);
        }
        
        return $token;
    }

    /**
     * Create random text 
     *
     * @param integer $length
     * @param string|null $keyspace
     * @return string
     */
    public static function random($length = 10, $keyspace = null) 
    {
        if (empty($keyspace) == true) {
            $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
    
        return \substr(\str_shuffle($keyspace),0,$length);
    }
}
