<?php

namespace NeoP\Annotation;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

use NeoP\Annotation\Annotation\Handler\HandlerInterface;
use NeoP\Annotation\Annotation\Mapping\AnnotationHandler;
use NeoP\Annotation\Annotation\Mapping\AnnotationMappingInterface;
use NeoP\Annotation\Exception\AnnotationException as NeoPAnnotationException;

use ReflectionClass;

class AnnotationRegister
{
    private static $annotations;

    private static $reader;

    public static function init(): void
    {
        self::registerLoader();
        self::registerReader();
    }

    private static function registerLoader(): void
    {
        AnnotationRegistry::registerLoader(function (string $class) {
            if (class_exists($class)) {
                return true;
            }

            return false;
        });
    }

    private static function registerReader(): void
    {
        self::$reader = new AnnotationReader();
    }

    public static function loadAnnotation(string $namespace, string $className)
    {
        $reflection = new ReflectionClass($className);
        
        // 判断是否是 Mapping
        if($reflection->implementsInterface(AnnotationMappingInterface::class)) {
            return ;
        }


        $className = $reflection->getName();

        // TODO 
        // 捕获注解异常

        // 获取类的注解 并 注入注解提供者
        $classAnnotations = self::$reader->getClassAnnotations($reflection);
        if( $classAnnotations ) {
            $isHandler = false;
            foreach ($classAnnotations as $classAnnotation) {
                if($reflection->implementsInterface(HandlerInterface::class) 
                    && $classAnnotation instanceof AnnotationHandler) {
                    AnnotationProvider::setHandlers($classAnnotation->getClass(), $className);
                    $isHandler = true;
                }
            }
            if(! $isHandler) {
                AnnotationProvider::setAnnotationClass($className, $classAnnotations, $reflection);
            }
            
            // 获取方法注解 并 注入注解提供者
            $methods = $reflection->getMethods();
            foreach($methods as $method) {
                $methodAnnotations = self::$reader->getMethodAnnotations($method);
                // 如果类没有注解 方法不能存在注解 因为不会将这个类加入容器
                if($methodAnnotations && !$classAnnotations) {
                    throw new NeoPAnnotationException("Property or method (Class: {$className}) with `@xxx` must be define class annotation");
                } else {
                    AnnotationProvider::setAnnotationMethod($className, $method->getName(), $methodAnnotations, $method);
                }
            }
            
            // 获取成员变量注解 并 注入注解提供者
            $properties = $reflection->getProperties();
            foreach($properties as $property) {
                $propertyAnnotations = self::$reader->getPropertyAnnotations($property);
                // 如果类没有注解 成员变量不能存在注解 因为不会将这个类加入容器
                if($propertyAnnotations && !$classAnnotations) {
                    throw new NeoPAnnotationException("Property or method (Class: {$className}) with `@xxx` must be define class annotation");
                } else {
                    AnnotationProvider::setAnnotationProperty($className, $property->getName(), $propertyAnnotations, $property);
                }
            }
        }
        

    }

    public static function registerAnnotation( string $key, $value )
    {
        self::$annotations[$key] = $value;
    }

    public static function getAnnotation( string $key )
    {
        return self::$annotations[$key];
    }
}