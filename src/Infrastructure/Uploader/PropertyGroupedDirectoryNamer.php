<?php

namespace App\Infrastructure\Uploader;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class PropertyGroupedDirectoryNamer implements DirectoryNamerInterface, ConfigurableInterface
{
    private int $modulo;
    private string $property;

    public function directoryName($object, PropertyMapping $mapping): string //@phpstan-ignore-line
    {
        $accessor = new PropertyAccessor();
        $value = $accessor->getValue($object, $this->property);

        return (string) ceil($value / $this->modulo);
    }

    public function configure(array $options): void //@phpstan-ignore-line
    {
        $options = array_merge(['property' => 'id', 'modulo' => 1000], $options);
        $this->property = $options['property'];
        $this->modulo = (int) $options['modulo'];
    }
}
