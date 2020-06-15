<?php

namespace NeoP\Annotation\Annotation\Handler;

use ReflectionClass;

abstract class Handler implements HandlerInterface
{
    protected $className;
    
    public function __construct(string $className)
    {
        $this->className = $className;
    }

}