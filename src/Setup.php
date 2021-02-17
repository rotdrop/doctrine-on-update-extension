<?php
/**
 * @author Claus-Justus Heine
 * @copyright 2021 Claus-Justus Heine <himself@claus-justus-heine.de>
 */

namespace CJH\Doctrine\Extensions;

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class DoctrineExtensions
 *
 * Register annotations.
 */
final class Setup
{
  /**
   * Include all annotations
   */
  public static function registerAnnotations():void
  {
    AnnotationRegistry::registerFile(__DIR__.'/Mapping/Annotation/ForeignKey.php');
  }
}

// Local Variables: ***
// c-basic-offset: 2 ***
// indent-tabs-mode: nil ***
// End: ***
