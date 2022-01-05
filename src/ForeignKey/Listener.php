<?php
/**
 * @author Claus-Justus Heine
 * @copyright 2021, 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
 */

namespace CJH\Doctrine\Extensions\ForeignKey;

use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\ToolEvents;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\Mapping\MappingException;

use Gedmo\Mapping\MappedEventSubscriber;

class Listener extends MappedEventSubscriber
{
  /** @var EntitiyManagerInterface */
  private $entityManager;

  private const CASCADE_OPTIONS = [
    'RESTRICT', // default
    'CASCADE',
    'SET NULL',
    'NO ACTION',
    'SET DEFAULT',
  ];

  public function __construct(EntityManagerInterface $entityManager)
  {
    parent::__construct();
    $this->entityManager = $entityManager;
  }

  /**
   * @return array
   */
  public function getSubscribedEvents()
  {
    return [
      ToolEvents::postGenerateSchemaTable,
      Events::loadClassMetadata,
    ];
  }

  /**
   * Maps additional metadata
   *
   * @return void
     */
  public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
  {
    $ea = $this->getEventAdapter($eventArgs);
    $this->loadMetadataForObjectClass($ea->getObjectManager(), $eventArgs->getClassMetadata());
  }

  /**
   * {@inheritdoc}
   */
  protected function getNamespace()
  {
    return __NAMESPACE__;
  }

  /**
   * Add "arbitrary" and in particular also "onUpdate" constraints to
   * "any" fields.
   *
   * @todo It is likely that there are edge-cases where this does not
   * work.
   */
  public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $args): void
  {
    $ea = $this->getEventAdapter($args);
    $ea->setEntityManager($this->entityManager);
    $om = $ea->getObjectManager();

    $meta = $args->getClassMetadata();
    $table = $args->getClassTable();

    if (!($config = $this->getConfiguration($om, $meta->name))) {
      return;
    }

    if (!isset($config['foreignKey'])) {
      return;
    }

    foreach ($config['foreignKey'] as $property => $references) {

      $columnNames = [];

      // now we need to determine the local column name(s)
      if (!empty($meta->fieldMappings[$property])) {
        $columnNames[] = $meta->fieldMappings[$property]['columnName'];
      } else if (!empty($meta->associationMappings[$property]['joinColumns'])) {
        $joinColumns = $meta->associationMappings[$property]['joinColumns'];
        $columnNames = array_map(function($joinColumn) { return $joinColumn['name']; }, $joinColumns);
      }

      if (empty($columnNames)) {
        throw new \RuntimeException('No mapping information for field "'.$property.'"');
      }

      foreach ($references as $reference) {

        if (empty($reference->name)) {
          if (count($columnNames) > 1) {
            throw new \InvalidArgumentException('Association field has more than two columns, you need to specify the local column name with "name=COLUMN"');
          }
          $columnName = $columnNames[0];
        } else {
          $columnName = $reference->name;
          if (array_search($columnName, $columnNames) === false) {
            throw new \InvalidArgumentException('Local column name "'.$columnName.'" not found');
          }
        }

        if (empty($reference->targetEntity)) {
          throw new \InvalidArgumentException('Missing target entity');
        }

        $targetEntity = $meta->fullyQualifiedClassName($reference->targetEntity);
        $targetMeta = $om->getClassMetadata($targetEntity);

        if (empty($reference->referencedColumnName)) {
          throw new \InvalidArgumentException('Missing target column field');
        }
        $referencedColumn = $reference->referencedColumnName;

        if (empty($targetMeta->fieldNames[$referencedColumn])) {

          // can still be an association mapping
          $found = false;
          foreach ($targetMeta->associationMappings as $association) {
            foreach ($association['joinColumns'] as $joinColumn) {
              if ($joinColumn['name'] === $referencedColumn) {
                $found = true;
                break 2;
              }
            }
          }
          if (!$found) {
            throw new \InvalidArgumentException('Nothing known about column "'.$referencedColumn.'" of entity "'.$targetEntity.'"');
          }
        }

        // optional cascading options with default 'restrict'

        $options = [];
        foreach (['onUpdate', 'onDelete'] as $cascadeOption) {
          $option = self::CASCADE_OPTIONS[0];
          if (!empty($reference->{$cascadeOption})) {
            $option = strtoupper($reference->{$cascadeOption});
            if (array_search($option, self::CASCADE_OPTIONS) === false) {
              throw new \InvalidArgumentException('Unknown "'.$cascadeOption.'" option: "'.$option.'"');
            }
            $options[$cascadeOption] = $option;
          }
        }

        $constraintName = $reference->constraintName;

        $referencedTable = $targetMeta->getTableName();
        $table->addForeignKeyConstraint(
          $referencedTable,
          [ $columnName ],
          [ $referencedColumn ],
          $options,
          $constraintName);

      }
    }
  }
}

// Local Variables: ***
// c-basic-offset: 2 ***
// indent-tabs-mode: nil ***
// End: ***
