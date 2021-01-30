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

use Arikaim\Core\Interfaces\Job\JobInterface;
use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;
use Arikaim\Core\Interfaces\ExtensionInterface;

/**
 * Factory class 
 */
class Factory 
{
    const EXTENSIONS_NAMESPACE      = 'Arikaim\\Extensions';
    const MODULES_NAMESAPCE         = 'Arikaim\\Modules';
    const CONTROLLERS_NAMESPACE     = CORE_NAMESPACE . '\\Controllers';
    const API_CONTROLLERS_NAMESPACE = CORE_NAMESPACE . '\\Api';
    const INTERFACES_NAMESPACE      = CORE_NAMESPACE . '\\Interfaces';

    /**
     * Set core namspace
     *
     * @param string $namespace
     * @return void
     */
    public static function setCoreNamespace(string $namespace): void
    {
        if (\defined('CORE_NAMESPACE') == false) {
            \define('CORE_NAMESPACE',$namespace);
        }
    }

    /**
     * Create object
     *
     * @param string $class
     * @param array|null $args
     * @param string|null $extension
     * @return object|null
     */
    public static function createInstance(string $class, ?array $args = null, ?string $extension = null)
    {
        if (empty($extension) == false) {
            $class = Self::getExtensionClassName($extension,$class);  
        }
        if (\class_exists($class) == false) {
            return null;
        }       
        $instance = (empty($args) == false) ? new $class(...$args) : new $class();           
           
        return $instance;            
    }

    /**
     * Create validator rule
     *
     * @param string $name
     * @param array|null $args
     * @return Arikaim\Core\Validator\Interfaces\RuleInterface
     */
    public static function createRule(string $name, ?array $args = null)
    {              
        $class = \ucfirst($name);

        return Self::createInstance(Self::getValidatorRuleClass($class),$args);            
    }

    /**
     * Create db schema object
     *
     * @param string $schemaClass
     * @param string $extension
     * @return object|null
     */
    public static function createSchema(string $schemaClass, ?string $extension = null)
    {
        $schemaClass = Self::getSchemaClass($schemaClass,$extension);    
        $instance = Self::createInstance($schemaClass);
        
        return $instance;
    }

    /**
     * Get class constant
     *
     * @param string $class
     * @param string $name
     * @return mixed
     */
    public static function getConstant(string $class, string $name)
    {
        return \constant($class . '::' . $name);
    }

    /**
     * Create module object
     *
     * @param string $module
     * @param string $class
     * @param array|null $args
     * @return object|null
     */
    public static function createModule(string $module, string $class, ?array $args = null)
    {
        $moduleClass = Self::getModuleClass($module,$class);
      
        return Self::createInstance($moduleClass,$args);             
    }

    /**
     * Create extension
     *
     * @param string $extension
     * @param string $class
     * @param array $args
     * @return object|null
     */
    public static function createExtension(string $extension, string $class, ?array $args = null)
    {
        $class = Self::getExtensionClassName($extension,$class);  
        $instance = Self::createInstance($class,$args);       

        return ($instance instanceof ExtensionInterface) ? $instance : null;                 
    }

    /**
     * Create Job
     *
     * @param string $class
     * @param string|null $extension
     * @param string|null $name
     * @return JobInterface|null
     */
    public static function createJob(string $class, ?string $extension = null, ?string $name = null): ?JobInterface
    {  
        if (\class_exists($class) == false) {
            $class = Self::getJobClassName($class,$extension);
        }
        
        $params = [$extension,$name];
        $job = Self::createInstance($class,$params);
       
        return ($job instanceof JobInterface) ? $job : null;
    }
    
    /**
     * Get event subscriber full class name
     *
     * @param string $baseClass
     * @param string|null $extension
     * @return string
     */
    public static function getEventSubscriberClass(string $baseClass, ?string $extension = null): string
    {
        if (empty($extension) == true) {
            return Self::getSystemEventsNamespace() . '\\' . $baseClass;
        } 
        
        return Self::getExtensionEventSubscriberClass($baseClass,$extension);        
    }

    /**
     * Create event subscriber
     *
     * @param string $baseClass
     * @param string|null $extension
     * @return object|null
     */
    public static function createEventSubscriber(string $baseClass, ?string $extension = null)
    {        
        $class = Self::getEventSubscriberClass($baseClass,$extension);         
        $instance = Self::createInstance($class);
        
        return ($instance instanceof EventSubscriberInterface) ? $instance : null;         
    }

    /**
     * Get class namspace
     *
     * @param string $class
     * @return string
     */
    public static function getClassNamespace(string $class): string 
    {           
        return \substr($class,0,\strrpos($class,'\\'));       
    } 

    /**
     * Get full core class name
     *
     * @param string $class
     * @return string
     */
    public static function getFullClassName(string $class): string
    {
        return CORE_NAMESPACE . '\\' . $class;
    }

    /**
     * Get module namespace
     *
     * @param string $module
     * @return string
     */
    public static function getModuleNamespace(string $module): string
    {
        return Self::MODULES_NAMESAPCE . '\\' . \ucfirst($module);
    }

    /**
     * Get module full class name
     *
     * @param string $module
     * @param string $baseClass
     * @return string
     */
    public static function getModuleClass(string $module, string $baseClass): string
    {
        return Self::getModuleNamespace($module) . '\\' . $baseClass;
    }

    /**
     * Get extension controller full class name
     *
     * @param string|null $extension
     * @param string $baseClass
     * @return string
     */
    public static function getExtensionControllerClass(?string $extension, string $baseClass): string
    {        
        return Self::getExtensionControllersNamespace(\ucfirst($extension)) . '\\' . $baseClass;
    }

    /**
     * Create controller
     *
     * @param Container $container
     * @param string $baseClass
     * @param string|null $extension
     * @return Controller|null
     */
    public static function createController($container, string $baseClass, ?string $extension)
    {
        $class = Self::getExtensionControllerClass($extension,$baseClass);

        return (\class_exists($class) == true) ? new $class($container) : null;
    }

    /**
     * Get extension controller namespace
     *
     * @param string|null $extension
     * @return string
     */
    public static function getExtensionControllersNamespace(?string $extension): string
    {
        return Self::getExtensionNamespace($extension) . '\\Controllers';
    }

    /**
     * Get extension subscriber full class name
     *
     * @param string $baseClass
     * @param string|null $extension
     * @return string
     */
    public static function getExtensionEventSubscriberClass(string $baseClass, ?string $extension): string
    {
        return Self::getExtensionSubscribersNamespace($extension) . '\\' . $baseClass;
    }

    /**
     * Get extension namespace
     *
     * @param string|null $extension
     * @return string
     */
    public static function getExtensionNamespace(?string $extension): string 
    {          
        return Self::EXTENSIONS_NAMESPACE . '\\' . \ucfirst($extension);
    }

    /**
     * Get extension full class name
     *
     * @param string|null $extension
     * @param string $baseClass
     * @return string
     */
    public static function getExtensionClassName(?string $extension, string $baseClass): string
    {
        return Self::getExtensionNamespace($extension) . '\\' . $baseClass;
    }

    /**
     * Get module console command full class name
     *
     * @param string $module
     * @param string $baseClass
     * @return string
     */
    public static function getModuleConsoleClassName(string $module, string $baseClass): string
    {
        return Self::getModuleNamespace($module) . '\\Console\\' . $baseClass;
    }

    /**
     * Get extension console command full class name
     *
     * @param string|null $extension
     * @param string $baseClass
     * @return string
     */
    public static function getExtensionConsoleClassName(?string $extension, string $baseClass): string
    {
        return Self::getExtensionNamespace($extension) . '\\Console\\' . $baseClass;
    }

    /**
     * Get full interface name
     *
     * @param string $baseName
     * @return string
     */
    public static function getFullInterfaceName(string $baseName): string
    {
        return Self::INTERFACES_NAMESPACE . '\\' . $baseName;
    }

    /**
     * Get job full class name
     *   
     * @param string $class
     * @param string|null $extension
     * @return string
     */
    public static function getJobClassName(string $class, ?string $extension): string
    {
        return Self::getJobsNamespace($extension) . '\\' . $class;
    }

    /**
     * Get job namespace
     *
     * @param string|null $extension
     * @return string
     */
    public static function getJobsNamespace(?string $extension = null): string
    {
        if (empty($extension) == false) {
            return Self::getExtensionNamespace($extension) . '\\Jobs';
        }

        return CORE_NAMESPACE . '\\Jobs';
    }

    /**
     * Get model full class name
     *
     * @param string $class
     * @param string|null $extension
     * @return string
     */
    public static function getModelClass(string $class,?string $extension = null): string 
    {
        if (empty($extension) == true) {
            return CORE_NAMESPACE . '\\Models\\' . $class;
        }
    
        return Self::getExtensionModelNamespace($extension) . '\\' . $class;
    }
    
    /**
     * Get extension namespace
     *
     * @param string|null $extension
     * @return string
     */
    public static function getExtensionModelNamespace(?string $extension): string
    {   
        return Self::getExtensionNamespace($extension) . '\\Models';
    }

    /**
     * Get controller full class name
     *
     * @param string $class
     * @return string
     */
    public static function getControllerClass(string $class): string
    {
        return Self::CONTROLLERS_NAMESPACE . '\\' . $class;
    }

    /**
     * Get validator rule full class name
     *
     * @param string $baseClass
     * @return string
     */
    public static function getValidatorRuleClass(string $baseClass): string
    {
        $class = CORE_NAMESPACE . '\\Validator\\Rule\\' . $baseClass;
        if (\class_exists($class) == false) {
            $class = CORE_NAMESPACE . '\\Validator\\Rule\\Db\\' . $baseClass;
        }

        return $class;
    }

    /**
     * Get validator filter full class name
     *
     * @param string $baseClass
     * @return string
     */
    public static function getValidatorFiltersClass(string $baseClass): string
    {
        return CORE_NAMESPACE . '\\Validator\\Filter\\' . $baseClass; 
    }

    /**
     * Get system events namespace
     *
     * @return string
     */
    public static function getSystemEventsNamespace(): string
    {
        return CORE_NAMESPACE . '\\Events';
    }

    /**
     * Get extension event subscribers namespace
     *
     * @param string|null $extension
     * @return string
     */
    public static function getExtensionSubscribersNamespace(?string $extension): string
    {
        return Self::getExtensionNamespace($extension) . '\\Subscribers';
    }

    /**
     * Get db schema namespace
     *
     * @param string|null $extension
     * @return string
     */
    public static function getSchemaNamespace(?string $extension = null): string
    {
        if ($extension != null) {           
            return Self::EXTENSIONS_NAMESPACE . '\\' . \ucfirst($extension) . '\\Models\\Schema\\';
        }
        
        return CORE_NAMESPACE . '\\Models\\Schema\\';
    }

    /**
     * Get db schema class
     *
     * @param string $baseClass
     * @param string|null $extension
     * @return string
     */
    public static function getSchemaClass(string $baseClass, ?string $extension): string
    {
        return Self::getSchemaNamespace($extension) . $baseClass;
    }
}
