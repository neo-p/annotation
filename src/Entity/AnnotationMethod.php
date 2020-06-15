<?php

namespace NeoP\Annotation\Entity;

use ReflectionMethod;

class AnnotationMethod
{
    private $name;
    private $annotations = [];
    private $reflectionMethod;

    function __construct(string $name, array $annotations = [], ReflectionMethod $reflectionMethod)
    {
        $this->name = $name;
        $this->annotations = $annotations;
        $this->reflectionMethod = $reflectionMethod;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $name;
    }

    public function setAnnotations(array $annotations)
    {
        $this->annotations = $annotations;
    }

    public function getAnnotations()
    {
        return $this->annotations;
    }

    public function getReflectionMethod()
    {
        return $this->reflectionMethod;
    }
}