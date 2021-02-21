<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Service\Serialization\GroupGenerator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraint;

/**
 * extract all field for a class and an action.
 */
class GroupResolver
{
    private const JOIN_ANNOTATIONS = [
        OneToOne::class => true,
        OneToMany::class => true,
        ManyToOne::class => true,
        ManyToMany::class => true,
    ];

    public const DEFAULT_GROUP = 'default';

    /**
     * @var array<string, bool>
     */
    private array $resolvedClass = [];

    private Reader $reader;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->reader = new AnnotationReader();
    }

    /**
     * @param class-string $class
     *
     * @return mixed[]
     *
     * @throws \ReflectionException
     */
    public function resolve(string $class, string $action): array
    {
        $this->resolvedClass = [];
        $properties = $this->getClassProperties($class);

        $groups = GroupGenerator::generateGroups($class, $action);

        return $this->readFields($properties, $class, $groups);
    }

    /**
     * @param \ReflectionProperty[] $fields
     * @param string[]              $groups
     *
     * @return mixed[]|null
     */
    private function readFields(array $fields, string $class, array $groups): ?array
    {
        if (isset($this->resolvedClass[$class])) {
            return null;
        }
        $this->resolvedClass[$class] = true;
        $allowedFields = [];

        foreach ($fields as $fieldProperty) {
            $this->readAttributes($fieldProperty, $groups, $allowedFields);
        }

        return $allowedFields;
    }

    /**
     * @param string[] $groups
     * @param mixed[]  $allowedFields
     */
    private function readAttributes(\ReflectionProperty $fieldProperty, array $groups, array &$allowedFields): void
    {
        $reflectionAttributes = $fieldProperty->getAttributes(Groups::class);

        foreach ($reflectionAttributes as $reflectionAttribute) {
            $annotation = $reflectionAttribute->newInstance();
            if (!$annotation instanceof Groups) {
                continue;
            }
            $diff = array_diff($groups, $annotation->getGroups());
            if (count($diff) === count($groups)) {
                continue;
            }
            $fieldName = $fieldProperty->getName();

            $this->extractSubClasses($fieldProperty, $groups, $allowedFields, $fieldName);
        }
    }

    /**
     * @return mixed[]
     */
    private function getJoinAnnotations(\ReflectionProperty $fieldProperty): array
    {
        $annotations = $this->reader->getPropertyAnnotations($fieldProperty);

        return array_filter($annotations, function (Annotation | Constraint $annotation) {
            return isset(self::JOIN_ANNOTATIONS[$annotation::class]);
        });
    }

    /**
     * @param string[] $groups
     * @param mixed[]  $allowedFields
     */
    private function extractSubClasses(\ReflectionProperty $fieldProperty, array $groups, array &$allowedFields, string $fieldName): void
    {
        /** @var Annotation[] $propertyJoinAnnotation */
        $propertyJoinAnnotation = $this->getJoinAnnotations($fieldProperty);
        if (1 !== count($propertyJoinAnnotation)) {
            $allowedFields[] = $fieldName;

            return;
        }
        /** @var OneToMany|OneToOne|ManyToMany|ManyToOne $joinAnnotation */
        $joinAnnotation = array_pop($propertyJoinAnnotation);
        /** @var class-string $subClass */
        $subClass = $joinAnnotation->targetEntity;

        $subProperties = $this->getClassProperties($subClass);
        $subFields = $this->readFields($subProperties, $subClass, $groups);
        if (null === $subFields) {
            return;
        }

        $allowedFields[$fieldName] = $subFields;
    }

    /**
     * @param class-string $class
     *
     * @return \ReflectionProperty[]
     *
     * @throws \ReflectionException
     */
    private function getClassProperties(string $class): array
    {
        $classReflection = new \ReflectionClass($class);

        return $classReflection->getProperties();
    }
}
