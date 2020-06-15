<?php

namespace NeoP\Annotation\Entity;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AnnotationClass
{
    private $class;
    private $annotations = [];
    private $reflectionClass = [];
    private $methods = [];
    private $properties = [];

    public function setClass(string $class)
    {
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setAnnotations(array $annotations)
    {
        $this->annotations = $annotations;
    }

    public function getAnnotations()
    {
        return $this->annotations;
    }

    public function setReflectionClass(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    public function setMethod(string $name, array $annotations, ReflectionMethod $reflectionMethod)
    {
        $this->methods[$name] = new AnnotationMethod($name, $annotations, $reflectionMethod);
    }
    
    public function getMethod(string $name)
    {
        return $this->methods[$name];
    }
    
    public function getMethods()
    {
        return $this->methods;
    }

    public function setProperty(string $name, array $annotations, ReflectionProperty &$reflectionProperty)
    {
        $this->properties[$name] = new AnnotationProperty($name, $annotations, $reflectionProperty);
    }
    
    public function getProperty(string $name)
    {
        return $this->properties[$name];
    }
    
    public function getProperties()
    {
        return $this->properties;
    }
}