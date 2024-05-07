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

use Arikaim\Core\Utils\File;
use \ZipArchive;
use Exception;

/**
 * Zip file helpers
 */
class ZipFile 
{
    /**
     * Extract zip arhive
     *
     * @param string $file
     * @param string $destination
     * @param array|string|int|null $files
     * @throws Exception
     * @return bool
     */
    public static function extract(string $file, string $destination, $files = null): bool
    {
        $zip = new ZipArchive;
        $result = $zip->open($file);
        if ($result !== true) {
            throw new Exception("Zip file not valid", 1);
        }

        if (File::isWritable($destination) == false) {
            File::setWritable($destination);
        }

        if (\is_integer($files) == true) {
            $item = $zip->getNameIndex($files);
            $files = [$item];
        }

        $result = $zip->extractTo($destination,$files);
        $zip->close(); 

        return $result;
    }

    /**
     * Get zip file item name
     *
     * @param string $zipFile
     * @param int $index
     * @return string|null
     */
    public static function getItemPath(string $zipFile, int $index): ?string
    {
        $zip = new ZipArchive;
        $result = $zip->open($zipFile);
        if ($result !== true) {
            return null;
        }

        return $zip->getNameIndex($index);    
    }

    /**
     * Create zip arhive
     *
     * @param string $source
     * @param string $destination
     * @param array  $skipDir
     * @return boolean
     */
    public static function create(string $source, string $destination, array $skipDir = []): bool
    { 
        $skipDir = $skipDir ?? ['.htaccess','.gitkeep','.git'];

        $zip = new ZipArchive();
        if ($zip->open($destination,ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {          
            return false;
        }

        if (\is_dir($source) == true) {
            $files = File::scanDirRecursive($source,function ($file, $key, $iterator) use ($skipDir) {
                if ($iterator->hasChildren() && !in_array($file->getFilename(),$skipDir)) {
                    return true;
                }
                return $file->isFile();
            });

            $relativePath = '';
            foreach ($files as $file) {     
                $relativePath = \str_replace($source,'',$file->getPathname());
                if ($file->isDir() == false) {       
                    $zip->addFile($file->getRealPath(),$relativePath ); 
                }
            }
        } else {
            $zip->addFile($source);
        }

        $zip->close();
    
        return ($zip->status == ZIPARCHIVE::ER_OK);          
    }

    /**
     * Check if zip arhive is valid
     *
     * @param string $file
     * @return boolean
     */
    public static function isValid(string $file): bool
    {      
        $zip = new ZipArchive();
        $result = $zip->open($file,ZipArchive::CHECKCONS);

        switch($result) {
            case ZipArchive::ER_NOZIP:
                return false;
            case ZipArchive::ER_INCONS:
                return false;
            case ZipArchive::ER_CRC:
                return false;
        }      

        return true;
    }    

    /**
     * Get zip error
     *
     * @param mixed $resource
     * @return string|null
     */
    public static function getZipError($resource): ?string
    {
        switch($resource) {
            case ZipArchive::ER_NOZIP :
                return 'Not a zip archive';              
            case ZipArchive::ER_INCONS :
                return 'Consistency check failed';               
            case ZipArchive::ER_CRC :
                return 'Checksum failed';                          
        }   

        return null;
    }

    /**
     * Get zip file files
     *
     * @param string $zipFile
     * @return array|null
     */
    public static function getFiles(string $zipFile): ?array
    {
        $zip = new ZipArchive;
        $result = $zip->open($zipFile);
        if ($result !== true) {
            return null;
        }

        $files = [];
        for($index = 0; $index < $zip->numFiles; $index++) {
            $files[] = $zip->getNameIndex($index);            
        }

        return $files;
    }
}
