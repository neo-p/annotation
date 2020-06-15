<?php

namespace NeoP\Annotation\Annotation\Mapping;

use NeoP\Annotation\Annotation\Mapping\AnnotationMappingInterface;

use function annotationBind;

/** 
 * Class AnnotationHandler
 * 
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("class", type="string"),
 * })
 */
final class AnnotationHandler implements AnnotationMappingInterface
{
    private $class;

    function __construct($params)
    {
        annotationBind($this, $params, 'setClass');
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}