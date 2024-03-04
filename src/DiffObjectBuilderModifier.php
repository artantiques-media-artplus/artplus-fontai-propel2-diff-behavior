<?php
namespace Fontai;

use Propel\Generator\Model\Behavior;
use Propel\Generator\Util\PhpParser;


class DiffObjectBuilderModifier
{
  protected $behavior;
  protected $column;

  public function __construct(Behavior $behavior)
  {
    $this->behavior = $behavior;
  }

  public function objectMethods($builder)
  {
    $script  = '';
    $script .= $this->addDiff($builder);

    return $script;
  }

  protected function addDiff($builder)
  {
    $foreignFields = [];

    foreach ($builder->getTable()->getForeignKeys() as $foreignKey)
    {
      $foreignTable = $foreignKey->getForeignTable();

      $foreignFields[$foreignKey->getLocalColumn()->getName()] = [
        $builder->getFKPhpNameAffix($foreignKey),
        isset($foreignTable->getBehaviors()['i18n'])
      ];
    }

    return $this->behavior->renderTemplate('objectDiff', [
      'tableMapClassName' => $builder->getTableMapClassName(),
      'objectClassName'   => $builder->getObjectClassName(),
      'foreignFields'     => $foreignFields
    ]);
  }
}