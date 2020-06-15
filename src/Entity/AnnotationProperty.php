<?php

namespace NeoP\Annotation\Entity;

use ReflectionProperty;

class AnnotationProperty
{
    private $name;
    private $annotations = [];
    private $reflectionProperty;

    function __construct(string $name, array $annotations = [], ReflectionProperty $reflectionProperty)
    {
        $this->name = $name;
        $this->annotations = $annotations;
        $this->reflectionProperty = $reflectionProperty;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $name;
    }

    public function setAnnotations(string $annotations)
    {
        $this->annotations = $annotations;
    }

    public function getAnnotations()
    {
        return $this->annotations;
    }
    
    public function getReflectionProperty()
    {
        return $this->reflectionProperty;
    }
}