<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting;

use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Darvin\ContentBundle\Translatable\TranslationJoinerInterface;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;

/**
 * Sorted by entity joiner
 */
class SortedByEntityJoiner implements SortedByEntityJoinerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslationJoinerInterface
     */
    private $translationJoiner;

    /**
     * @param \Doctrine\ORM\EntityManager                                     $em                  Entity manager
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     * @param \Darvin\ContentBundle\Translatable\TranslationJoinerInterface   $translationJoiner   Translation joiner
     */
    public function __construct(
        EntityManager $em,
        TranslatableManagerInterface $translatableManager,
        TranslationJoinerInterface $translationJoiner
    ) {
        $this->em = $em;
        $this->translatableManager = $translatableManager;
        $this->translationJoiner = $translationJoiner;
    }

    /**
     * {@inheritdoc}
     */
    public function joinEntity(QueryBuilder $qb, $sortedByPropertyPath, $locale)
    {
        if (empty($sortedByPropertyPath)) {
            return;
        }

        $rootEntities = $qb->getRootEntities();

        if (count($rootEntities) > 1) {
            throw new SortingException('Only single root entity query builders are supported.');
        }

        $entityClass = $rootEntities[0];

        try {
            $doctrineMeta = $this->em->getClassMetadata($entityClass);
        } catch (MappingException $ex) {
            throw new SortingException(sprintf('Unable to get Doctrine metadata for class "%s".', $entityClass));
        }

        $parts = explode('.', $sortedByPropertyPath);
        $partsCount = count($parts);

        if (!in_array($partsCount, array(2, 3))) {
            throw new SortingException(sprintf('Property path must consist of 2 or 3 parts, %d provided.', $partsCount));
        }

        $rootAliases = $qb->getRootAliases();

        $method = sprintf('joinBy%dPartsPropertyPath', $partsCount);
        $this->$method($qb, $parts, $locale, $doctrineMeta, $rootAliases[0]);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder              $qb                Query builder
     * @param array                                   $propertyPathParts Property path parts
     * @param string                                  $locale            Locale
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $doctrineMeta      Doctrine metadata
     * @param string                                  $qbRootAlias       Query builder root alias
     *
     * @throws \Darvin\ContentBundle\Sorting\SortingException
     */
    protected function joinBy2PartsPropertyPath(
        QueryBuilder $qb,
        array $propertyPathParts,
        $locale,
        ClassMetadataInfo $doctrineMeta,
        $qbRootAlias
    ) {
        $firstPart = $propertyPathParts[0];

        if ('o' === $firstPart) {
            return;
        }
        if ($this->translatableManager->isTranslatable($doctrineMeta->getName())
            && $firstPart === $this->translatableManager->getTranslationsProperty()
        ) {
            $this->translationJoiner->joinTranslation($qb, $locale);

            return;
        }
        if (!$doctrineMeta->hasAssociation($firstPart)) {
            throw $this->createPropertyIsNotAssociationException($doctrineMeta->getName(), $firstPart);
        }

        $qb
            ->addSelect($firstPart)
            ->leftJoin($qbRootAlias.'.'.$firstPart, $firstPart);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder              $qb                Query builder
     * @param array                                   $propertyPathParts Property path parts
     * @param string                                  $locale            Locale
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $doctrineMeta      Doctrine metadata
     *
     * @throws \Darvin\ContentBundle\Sorting\SortingException
     */
    protected function joinBy3PartsPropertyPath(
        QueryBuilder $qb,
        array $propertyPathParts,
        $locale,
        ClassMetadataInfo $doctrineMeta
    ) {
        list($firstPart, $secondPart) = $propertyPathParts;

        if (!$doctrineMeta->hasAssociation($firstPart)) {
            throw $this->createPropertyIsNotAssociationException($doctrineMeta->getName(), $firstPart);
        }

        $associatedEntity = $doctrineMeta->associationMappings[$firstPart]['targetEntity'];

        if (is_subclass_of($doctrineMeta->getName(), $associatedEntity)) {
            $associatedEntity = $doctrineMeta->getName();
        }
        if (!$this->translatableManager->isTranslatable($associatedEntity)) {
            $message = sprintf(
                'Entity class "%s" must be translatable in order to sort by 3 parts property path.',
                $associatedEntity
            );

            throw new SortingException($message);
        }

        $translationsProperty = $this->translatableManager->getTranslationsProperty();

        if ($secondPart !== $translationsProperty) {
            throw new SortingException(
                sprintf('Translations property must have name "%s", "%s" provided.', $translationsProperty, $secondPart)
            );
        }

        $this->translationJoiner->joinTranslation($qb, $locale, $firstPart);
    }

    /**
     * @param string $entityClass Entity class
     * @param string $property    Property name
     *
     * @return \Darvin\ContentBundle\Sorting\SortingException
     */
    private function createPropertyIsNotAssociationException($entityClass, $property)
    {
        return new SortingException(sprintf('Property "%s::$%s" is not valid association.', $entityClass, $property));
    }
}
