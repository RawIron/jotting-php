<?php


function staticSetter($property)
{
    $propertyLc = $property->name;
    $propertyUc = ucfirst($property->name);

    return <<<CODE

  public static final function set$propertyUc(\$value)
  {
    self::\$$propertyLc = \$value;
  }

CODE;

}


function instanceSetter($property)
{
    $propertyLc = $property->name;
    $propertyUc = ucfirst($property->name);

    return <<<CODE

  public function set$propertyUc(\$value)
  {
    \$this->$propertyLc = \$value;
    return \$this;
  }

CODE;

}

function generateSetters($klass)
{
    $me = new ReflectionClass($klass);

    $phpSetterCode = "";
    foreach($me->getProperties() as $property) {
        if ($property->isStatic()) {
            $phpSetterCode .= staticSetter($property);
        } else {
            $phpSetterCode .= instanceSetter($property);
        }
    }
    return $phpSetterCode;
}


print(generateSetters('My_Class'));
