<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     MIT http://opensource.org/licenses/MIT
 */

namespace Mautic;

class AutoLoader
{

    static private $classNames = array();

    public static function registerDirectory ($dirName, $mainDir = '')
    {
        $di = new \DirectoryIterator($dirName);

        if (empty($mainDir)) {
            $mainDir = $dirName;
        }

        foreach ($di as $file) {
            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                self::registerDirectory($file->getPathname(), $mainDir);
            } elseif (substr($file->getFilename(), -4) === '.php') {
                $namespace = 'Mautic' . str_replace('/', '\\', substr(str_replace($mainDir, '', $file->getPathname()), 0, -4));
                AutoLoader::registerClass($namespace, $file->getPathname());
            }
        }
    }

    public static function registerClass ($className, $fileName)
    {
        AutoLoader::$classNames[$className] = $fileName;
    }

    public static function loadClass ($className)
    {
        if (isset(AutoLoader::$classNames[$className])) {
            require_once AutoLoader::$classNames[$className];
        }
    }
}

spl_autoload_register(array('Mautic\AutoLoader', 'loadClass'));

AutoLoader::registerDirectory(__DIR__);
