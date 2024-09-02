<?php

/**
 * @author Claus-Justus Heine
 * @copyright 2021, 2024 Claus-Justus Heine <himself@claus-justus-heine.de>
 */
namespace CJH\Doctrine\Extensions\Mapping\Annotation;

use Attribute;
use ReflectionClass;
use ReflectionProperty;

use Gedmo\Mapping\Annotation\Annotation as GedmoAnnotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ForeignKey implements GedmoAnnotation
{
  /** {@inheritdoc} */
  public function __construct(
    array $data = [],
    public ?string $targetEntity = null,
    public ?string $constraintName = null,
    public ?string $name = null,
    public ?string $referencedColumnName = null,
    public ?string $onUpdate = null,
    public ?string $onDelete = null,
  ) {
    $properties = (new ReflectionClass(__CLASS__))
      ->getProperties(ReflectionProperty::IS_PUBLIC);
    /** @var ReflectionProperty $property */
    foreach ($properties as $property) {
      $name = $property->getName();
      if (isset($data[$name])) {
        $this->{$name} = $data[$name];
      }
    }
  }
}
