<?php
/**
 * @author Claus-Justus Heine
 * @copyright 2021 Claus-Justus Heine <himself@claus-justus-heine.de>
 */

namespace CJH\Doctrine\Extensions\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class ForeignKey extends Annotation
{
  /**
   * @var string
   * @Required
   */
  public $targetEntity;

  /**
   * @var string
   */
  public $constraintName;

  /**
   * @var string
   */
  public $name;

  /**
   * @var string
   * @Required
   */
  public $referencedColumnName;

  /**
   * @var string
   */
  public $onUpdate;

  /**
   * @var string
   */
  public $onDelete;
}

// Local Variables: ***
// c-basic-offset: 2 ***
// indent-tabs-mode: nil ***
// End: ***
