<?php

namespace NeoP\Annotation;

use NeoP\Annotation\Entity\AnnotationClass;
use NeoP\Annotation\Exception\AnnotationException;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AnnotationProvider
{

    private static $annotationSource;
    private static $handlers;

    public static function setHandlers(string $mapping, string $handler) {
        self::$handlers[$mapping] = $handler;
    }

    public static function getHandlers() {
        return self::$handlers;
    }

    public static function getHandler(string $mapping) {
        if(! isset(self::$handlers[$mapping])) {
            throw new AnnotationException("No handler exists for annotation {$mapping}");
        }
        return self::$handlers[$mapping];
    }

    public static function setAnnotationClass(string $class, array $annotations, ReflectionClass $reflectionClass): void
    {
        if(! isset(self::$annotationSource[$class])) {
            $annotationClass = new AnnotationClass();
            $annotationClass->setClass($class);
            $annotationClass->setReflectionClass($reflectionClass);
            $annotationClass->setAnnotations($annotations);
            self::$annotationSource[$class] = $annotationClass;
        } else {
            self::$annotationSource[$class]->setClass($class);
            self::$annotationSource[$class]->setReflectionClass($reflectionClass);
            self::$annotationSource[$class]->setAnnotations($annotations);
        }
    }

    public static function setAnnotationMethod(string $class, string $method, array $annotations, ReflectionMethod $reflectionMethod): void
    {
        if(! isset(self::$annotationSource[$class])) {
            $annotationClass = new AnnotationClass();
            $annotationClass->setMethod($method, $annotations, $reflectionMethod);
            self::$annotationSource[$class] = $annotationClass;
        } else {
            self::$annotationSource[$class]->setMethod($method, $annotations, $reflectionMethod);
        }
    }

    public static function setAnnotationProperty(string $class, string $property, array $annotations, ReflectionProperty $reflectionProperty): void
    {
        if(! isset(self::$annotationSource[$class])) {
            $annotationClass = new AnnotationClass();
            $annotationClass->setProperty($property, $annotations, $reflectionProperty);
            self::$annotationSource[$class] = $annotationClass;
        } else {
            self::$annotationSource[$class]->setProperty($property, $annotations, $reflectionProperty);
        }
    }

    public static function getAnnotations(string $class = "")
    {
        if($class == "") {
            return self::$annotationSource;
        }
        return self::$annotationSource[$class];
    }


}