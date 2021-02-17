<?php
/**
 * @author Claus-Justus Heine
 * @copyright 2021 Claus-Justus Heine <himself@claus-justus-heine.de>
 */

namespace CJH\Doctrine\Extensions\ForeignKey\Mapping\Driver;

use CJH\Doctrine\Extensions\Mapping\Annotation\ForeignKey as AnnotationClass;

use Gedmo\Mapping\Driver\AbstractAnnotationDriver;
use Doctrine\ORM\Mapping\ClassMetadata;

class Annotation extends AbstractAnnotationDriver
{
  const ANNOTATION = AnnotationClass::class;

  /**
   * {@inheritDoc}
   */
  public function readExtendedMetadata($meta, array &$config)
  {
    $class = $this->getMetaReflectionClass($meta);
    foreach ($class->getProperties() as $property) {
      $field = $property->getName();
      if ($meta->isMappedSuperclass && !$property->isPrivate()) {
        continue;
      }

      $annotations = $this->reader->getPropertyAnnotations($property);
      foreach ($annotations as $annotation) {
        if ($annotation instanceof AnnotationClass) {
          $config['foreignKey'][$field][] = $annotation;
        }
      }
    }
  }

}

// Local Variables: ***
// c-basic-offset: 2 ***
// indent-tabs-mode: nil ***
// End: ***
