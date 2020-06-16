<?php

namespace NeoP\Annotation\Scaner;

use NeoP\Stdlib\Composer;
use NeoP\Application;
use NeoP\Component\ComponentInterface;
use NeoP\Component\ComponentRegister;
use NeoP\Annotation\AnnotationRegister;
use NeoP\Log\Log;
use NeoP\Annotation\Exception\AnnotationException;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use InvalidArgumentException;

use function \is_null;

class Scaner
{
    const SCAN_SUFFIX = 'php';

    const COMPONENT = 'Component';

    private $ignoreScan = [];

    private $scanning = [];

    private function scanPath()
    {
        $self = dirname(dirname(dirname(__DIR__)));
        $this->scanning = service("scan", []);
        if(is_string($this->scanning)) {
            $this->scanning = [$this->scanning];
        }

        if(! is_array($this->scanning)) {
            throw new AnnotationException("service config is not a array.");
        }

        $this->scanning = array_merge($this->scanning, [$self, Application::$service]);
    }

    public function run() 
    {
        
        $scanStart = \microtime(true);
        $this->scanPath();
        $this->scan();
        $scanEnd = \microtime(true);
        $time = round(($scanEnd - $scanStart) * 1000, 2);
        Log::stdout("-------------------------------------------------", 0, Log::MODE_DEFAULT);
        Log::stdout("Scanning annotations takes a total of {$time} ms.", 0, Log::MODE_DEFAULT, Log::FG_YELLOW);
        Log::stdout("-------------------------------------------------", 0, Log::MODE_DEFAULT);
    }

    private function scan()
    {
        $dependsNum = 0;
        AnnotationRegister::init();
        $autoloader = Composer::getClassLoader();
        $psr4 = $autoloader->getPrefixesPsr4();
        foreach($psr4 as $namespace => $paths) {

            foreach($paths as $path) {
                $path = realpath($path).'/';
                $isIgnored = $this->isIgnored($path);
                if($isIgnored) {
                    continue;
                }
                $splFiles = $this->recursiveIterator($path);
                foreach( $splFiles as $key => $splFileInfo ) {

                    $filePath  = $splFileInfo->getPathName();
                    if (is_dir($filePath)) {
                        continue;
                    }

                    $fileName  = $splFileInfo->getFilename();
                    $extension = $splFileInfo->getExtension();
                    if ( strtolower(self::SCAN_SUFFIX) !== strtolower($extension) || strpos($fileName, '.') === 0) {
                        continue;
                    }

                    $suffix    = sprintf('.%s', $extension);
                    $pathName  = str_replace([$path, "/", $suffix], ["", "\\", ""], $filePath);
                    $pathName  = str_replace([$path, "/", $suffix], ["", "\\", ""], $filePath);
                    $className = sprintf('%s%s', $namespace, $pathName);
                    if( class_exists($className) ) {
                        AnnotationRegister::loadAnnotation($namespace, $className);
                        Log::stdout("-> Loading dependencies {$className} ", 0, Log::MODE_DEFAULT, Log::FG_BLUE);
                        $dependsNum++;
                    }
                }
            }
        }
        
        Log::stdout("-> Total number of loaded dependencies: {$dependsNum} ", 0, Log::MODE_DEFAULT, Log::FG_WHITE);
        // TODO
        // 释放 AnnotationRegister 的 $reader
    }

    private function isIgnored(string $path)
    {
        $root = getcwd();
        foreach ($this->scanning as $scanning) {
            $scanPath = $scanning;
            if (strpos($scanning, '/') !== 0) {
                $scanPath =  $root . '/' . $scanning;
            }
            if( strpos($path, $scanPath) === 0 ) {
                return false;
            }
        }
        return true;
    }

    private function getScanComponent(string $namespace)
    {
        $className = sprintf('%s%s', $namespace, self::COMPONENT);
        if( !class_exists($className) )
            return ;
        $class = new $className();
        return $class;
    }

    public static function recursiveIterator(
        string $path,
        int $mode = RecursiveIteratorIterator::LEAVES_ONLY,
        int $flags = 0
    ): RecursiveIteratorIterator {
        if (empty($path) || !file_exists($path)) {
            throw new InvalidArgumentException('File path is not exist! Path: ' . $path);
        }

        $directoryIterator = new RecursiveDirectoryIterator($path);

        return new RecursiveIteratorIterator($directoryIterator, $mode, $flags);
    }
}