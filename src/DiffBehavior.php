<?php
namespace Fontai;

use Propel\Generator\Model\Behavior;


class DiffBehavior extends Behavior
{
  /**
   * @var DiffObjectBuilderModifier
   */
  protected $objectBuilderModifier;

  /**
   * {@inheritdoc}
   */
  public function getObjectBuilderModifier()
  {
    if (NULL === $this->objectBuilderModifier) $this->objectBuilderModifier = new DiffObjectBuilderModifier($this);

    return $this->objectBuilderModifier;
  }
}