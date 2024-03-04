
/**
 * Makes difference of current entity with passed one
 *
 * @return array
 */
public function diff(<?= $objectClassName ?> $object)
{
  $modified = [];
  $foreignFields = <?php var_export($foreignFields); ?>;

  foreach (<?= $tableMapClassName ?>::getFieldNames(TableMap::TYPE_FIELDNAME) as $fieldName)
  {
    $getter = sprintf('get%s', <?= $tableMapClassName ?>::translateFieldName($fieldName, TableMap::TYPE_FIELDNAME, TableMap::TYPE_PHPNAME));

    $valueThis   = call_user_func([$this, $getter]);
    $valueObject = call_user_func([$object, $getter]);

    $valueThisToCompare = $valueThis instanceof \DateTimeInterface ? json_encode($valueThis) : $valueThis;
    $valueObjectToCompare = $valueObject instanceof \DateTimeInterface ? json_encode($valueObject) : $valueObject;

    if ($valueThisToCompare !== $valueObjectToCompare)
    {
      if (in_array($fieldName, ['id', 'created_at', 'updated_at']))
      {
        continue;
      }

      $data = [[$valueThis], [$valueObject]];

      if (isset($foreignFields[$fieldName]))
      {
        $getter = sprintf('get%s', $foreignFields[$fieldName][0]);

        if (!$foreignFields[$fieldName][1])
        {
          $data[0][1] = ($foreignObject = call_user_func([$this, $getter])) ? $foreignObject->__toString() : NULL;
          $data[1][1] = ($foreignObject = call_user_func([$object, $getter])) ? $foreignObject->__toString() : NULL;
        }
        else
        {
          $data[0][1] = [];
          $data[1][1] = [];

          foreach (LanguageQuery::create()->select('Code')->find() as $code)
          {
            $data[0][1][$code] = ($foreignObject = call_user_func([$this, $getter])) ? $foreignObject->getTranslation($code)->__toString() : NULL;
            $data[1][1][$code] = ($foreignObject = call_user_func([$object, $getter])) ? $foreignObject->getTranslation($code)->__toString() : NULL;
          }
        }
      }

      $modified[$fieldName] = $data;
    }
  }

  if (method_exists($this, 'getCurrentTranslation'))
  {
    $modified += $this->getCurrentTranslation()->diff($object->getCurrentTranslation());
  }

  return $modified;
}
