<?php

namespace App\Infrastructure\Storage\Naming;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class IdDirectoryNamer implements DirectoryNamerInterface
{

    public function directoryName($object, PropertyMapping $mapping): string
    {
        return strval(ceil($object->getId() / 1000));
    }
}
