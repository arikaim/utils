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

use Arikaim\Core\Utils\Text;
use Exception;

/**
 * Utility static functions
 */
class Utils 
{   
    /**
     * Return true if required version = current or < 
     *
     * @param string $currentVersion
     * @param string $requiredVersion
     * @return boolean
     */
    public static function checkVersion(string $currentVersion, string $requiredVersion): bool
    {
        $result = \version_compare(Self::formatVersion($currentVersion),Self::formatVersion($requiredVersion));

        return ($result == 0 || $result == 1);
    }

    /**
     * Return true if url is valid
     *
     * @param string $url
     * @return boolean
     */
    public static function isValidUrl(string $url): bool
    {
        return (\filter_var($url,FILTER_VALIDATE_URL) !== false);
    }

    /**
     * Return classes from php code
     *
     * @param string $phpCode
     * @return array
     */
    public static function getClasses($phpCode) 
    {
        $classes = [];
        $tokens = \token_get_all($phpCode);
        $count = \count($tokens);

        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS 
                && $tokens[$i - 1][0] == T_WHITESPACE 
                && $tokens[$i][0] == T_STRING 
                && !($tokens[$i - 3] 
                && $i - 4 >= 0 
                && $tokens[$i - 4][0] == T_ABSTRACT)) {               
                \array_push($classes,$tokens[$i][1]);
            }
        }

        return $classes;
    }

    /**
     * Get parent path
     *
     * @param string $path
     * @return string|false
     */
    public static function getParentPath($path)
    {
        if (empty($path) == true) {
            return false;
        }       
        $parentPath = \dirname($path);

        return ($parentPath == '.') ? false : $parentPath;          
    }

    /**
     * Create random key
     *
     * @return string
     */
    public static function createRandomKey()
    {
        return \md5(\uniqid(\rand(),true));
    }

    /**
     * Create unique token
     *
     * @param string $prefix
     * @param bolean $long
     * @return string
     */
    public static function createToken($prefix = '', $long = false)
    {
        $hash = \md5(\rand(1,10) . \microtime());
        $secondHash = \md5(\rand(1,10) . \microtime());
        $token = $prefix . $hash;
        
        return ($long == true) ? $token . '-' . $secondHash : $token;
    }
    
    /**
     * Return true if ip is valid.
     *
     * @param string $ip
     * @return boolean
     */
    public static function isValidIp($ip)
    {      
        return (\filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false);
    }

    /**
     * Check if class implement interface 
     *
     * @param object $obj
     * @param string $interfaceName
     * @return boolean
     */
    public static function isImplemented($obj, $interfaceName)
    {       
        $result = $obj instanceof $interfaceName;
        if ($result == true) {
            return true;
        }
        if (\is_object($obj) == false && \is_string($obj) == false) {
            return false;
        }

        foreach (\class_parents($obj) as $subClass) {
            if ($result == true) {
                break;
            }
            $result = Self::isImplemented($subClass, $interfaceName);
        } 

        return $result;
    }

    /**
     * Return constant value or default if constant not defined.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function constant($name, $default = null)
    {
        return (\defined($name) == true) ? \constant($name) : $default; 
    }

    /**
     * Convert path to url
     *
     * @param string $path
     * @return void
     */
    public static function convertPathToUrl($path) 
    {
        return \str_replace('\\','/',$path);
    }

    /**
     * Return true if text is valid JSON 
     *
     * @param string $text
     * @return boolean
     */
    public static function isJson($jsonText)
    {        
        try {
            if (\is_string($jsonText) == true) {
                return \is_array(\json_decode($jsonText,true));
            }         
        } catch(Exception $e) {
            return false;
        }

        return false;
    }
    
    /**
     * Encode array to JSON 
     *
     * @param array $data
     * @return string
     */
    public static function jsonEncode(array $data)
    {
        return \json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Clean JSON text
     *
     * @param string $text
     * @return string
     */
    public static function cleanJson($text)
    {
        for ($i = 0; $i <= 31; ++$i) {
            $text = \str_replace(\chr($i),'',$text);
        }
        $text = \str_replace(\chr(127),'',$text);
        $text = Self::removeBOM($text);
        $text = \stripslashes($text);
        $text = \htmlspecialchars_decode($text);

        return $text;
    }

    /**
     * Decode JSON text
     *
     * @param string $text
     * @param boolean $clean
     * @param boolean $toArray
     * @return array
     */
    public static function jsonDecode($text, $clean = true, $toArray = true)
    {        
        $text = ($clean == true) ? Self::cleanJson($text) : $text;

        return \json_decode($text,$toArray);
    }

    /**
     * Call static method
     *
     * @param string $class
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function callStatic($class, $method, $args = null)
    {     
        return (\is_callable([$class,$method]) == false) ? null : \forward_static_call([$class,$method],$args);
    }

    /**
     * Call object method
     *
     * @param object $obj
     * @param string $method
     * @param array|null $args
     * @return mixed
     */
    public static function call($obj, $method, $args = null)
    {
        if (\is_object($obj) == true) {
            $callable = [$obj,$method];
            $class = \get_class($obj);
        } else {
            $callable = $method; 
            $class = null;
        }

        if (\is_callable($callable) == false) {
            if ($class == null) {
                $class = $obj;
            }
            return Self::callStatic($class,$method,$args);  
        }
        return (\is_array($args) == true) ? \call_user_func_array($callable,$args) : \call_user_func($callable,$args);
    }

    /**
     * Return true if email is valid
     *
     * @param string $email
     * @return boolean
     */
    public static function isEmail($email)
    {
        return (\filter_var($email,FILTER_VALIDATE_EMAIL) !== false);
    }
    
    /**
     * Check if text contains thml tags
     *
     * @param string $text
     * @return boolean
     */
    public static function hasHtml($text)
    {
        return ($text != \strip_tags($text));
    }

    /**
     * Remove BOM from text
     *
     * @param string $text
     * @return void
     */
    public static function removeBOM($text)
    {        
        return (\strpos(\bin2hex($text),'efbbbf') === 0) ? \substr($text,3) : $text;
    }

    /**
     * Check if variable is empty
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isEmpty($var)
    {       
        return (\is_object($var) == true) ? empty((array)$var) : empty($var);
    }

    /**
     * Format version to full version format 0.0.0
     *
     * @param string|null $version
     * @return string
     */
    public static function formatVersion(?string $version): string
    {
        $version = $version ?? '1.0.0';
        $items = \explode('.',\trim($version));
        $release = $items[0] ?? $version;
        $major = $items[1] ?? '0';       
        $minor = $items[2] ?? '0';
           
        return $release . '.' . $major . '.' . $minor;
    }

    /**
     * Create key 
     *
     * @param string $text
     * @param string $pathItem
     * @param string $separator
     * @return string
     */
    public static function createKey($text, $pathItem = null, $separator = '.')
    {
        return (empty($pathItem) == true) ? $text : $text . $separator . $pathItem;     
    }

    /**
     * Return default if variable is empty
     *
     * @param mixed $variable
     * @param mixed $default
     * @return mixed
     */
    public function getDefault($variable, $default)
    {
        return (Self::isEmpty($variable) == true) ? $default : $variable;      
    }

    /**
     * Convert value to text
     *
     * @param mixed $value
     * @return string
     */
    public static function getValueAsText($value)
    {
        if (\gettype($value) == 'boolean') {           
            return ($value == true) ? 'true' : 'false'; 
        }       

        return '\'' . $value . '\'';
    }

    /**
     * Return true if variable is Closure
     *
     * @param mixed $variable
     * @return boolean
     */
    public static function isClosure($variable) 
    {
        return (\is_object($variable) && ($variable instanceof \Closure));
    }

    /**
     * Return true if text is utf8 encoded string
     *
     * @param mixed $text
     * @return boolean
     */
    public static function isUtf($text) {
        return (bool)\preg_match("//u",\serialize($text));
    }

    /**
     * Create slug
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    public static function slug($text, $separator = '-')
    {
        if (Self::isUtf($text) == true) {            
            $text = \trim(\mb_strtolower($text));
            // Replace umlauts chars
            $text = Text::replaceChars($text);
            $text = \str_replace(' ',$separator,$text);
            return $text;
        }
        $text = \trim(\strtolower($text));
        // Replace umlauts chars
        $text = Text::replaceChars($text);

        return \preg_replace(["/[^\w\s]+/", "/\s+/"],['',$separator],$text);
    } 

    /**
     * Get memory size text.
     *
     * @param integer $size
     * @param array $labels
     * @param boolean $asText
     * @return string|array
     */
    public static function getMemorySizeText($size, $labels = null, $asText = true)
    {        
        $labels = (\is_array($labels) == false) ? ['Bytes','KB','MB','GB','TB','PB','EB','ZB','YB'] : $labels;            
        $power = $size > 0 ? \floor(\log($size, 1024)) : 0;
        $result['size'] = \round($size / \pow(1024, $power),2);
        $result['label'] = $labels[$power];

        return ($asText == true) ? $result['size'] . ' ' . $result['label'] : $result; 
    }

    /**
     * Return base class name
     *
     * @param string|object $class
     * @return string
     */
    public static function getBaseClassName($class)
    {
        $class = \is_object($class) ? \get_class($class) : $class;
        $tokens = \explode('\\',$class);
        
        return \end($tokens);
    }

    /**
     * Get script execution time
     *
     * @return integer|false
     */
    public static function getExecutionTime($startTimeConstantName = 'APP_START_TIME') 
    {
        $startTime = (\defined($startTimeConstantName) == true) ? \constant($startTimeConstantName) : $_SERVER['REQUEST_TIME'];
        
        return (\microtime(true) - $startTime);  
    }
}
