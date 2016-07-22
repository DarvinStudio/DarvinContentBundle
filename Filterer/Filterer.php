<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Filterer;

use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Darvin\ContentBundle\Translatable\TranslationJoinerInterface;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filterer
 */
class Filterer implements FiltererInterface
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
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadataInfo[]
     */
    private $doctrineMetadata;

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
        $this->optionsResolver = new OptionsResolver();
        $this->doctrineMetadata = [];

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(QueryBuilder $qb, array $filterData = null, array $options = [], $conjunction = true)
    {
        if (empty($filterData)) {
            return;
        }
        foreach ($filterData as $field => $value) {
            if (null === $value) {
                unset($filterData[$field]);
            }
        }
        if (empty($filterData)) {
            return;
        }

        $rootAliases = $qb->getRootAliases();

        if (count($rootAliases) > 1) {
            throw new FiltererException('Only single root alias query builders are supported.');
        }
        try {
            $options = $this->optionsResolver->resolve($options);
        } catch (ExceptionInterface $ex) {
            throw new FiltererException(sprintf('Options are invalid: "%s".', $ex->getMessage()));
        }

        $this->addConstraints($qb, $filterData, $options, $conjunction, $rootAliases[0]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver Options resolver
     */
    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'non_strict_comparison_fields' => [],
            ])
            ->setAllowedTypes('non_strict_comparison_fields', 'array');
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb          Query builder
     * @param array                      $filterData  Filter data
     * @param array                      $options     Options
     * @param bool                       $conjunction Whether to use conjunction (otherwise - disjunction)
     * @param string                     $rootAlias   Query builder root alias
     */
    private function addConstraints(QueryBuilder $qb, array $filterData, array $options, $conjunction, $rootAlias)
    {
        $rootEntities = $qb->getRootEntities();
        $entityClass = $rootEntities[0];

        $where = [];

        foreach ($filterData as $field => $value) {
            $strictComparison = !in_array($field, $options['non_strict_comparison_fields']);

            $where[] = $this->buildConstraint($qb, $field, $entityClass, $rootAlias, $strictComparison);

            $qb->setParameter($field, $strictComparison ? $value : '%'.$value.'%');
        }

        $qb->andWhere(implode($conjunction ? ' AND ' : ' OR ', $where));
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb               Query builder
     * @param string                     $field            Constraint field
     * @param string                     $entityClass      Entity class
     * @param string                     $rootAlias        Query builder root alias
     * @param bool                       $strictComparison Whether to use strict comparison
     *
     * @return string
     * @throws \Darvin\ContentBundle\Filterer\FiltererException
     */
    private function buildConstraint(QueryBuilder $qb, $field, $entityClass, $rootAlias, $strictComparison)
    {
        $property = preg_replace('/(From|To)$/', '', $field);

        $doctrineMeta = $this->getDoctrineMetadata($entityClass);

        if (!isset($doctrineMeta->associationMappings[$property]) && !isset($doctrineMeta->fieldMappings[$property])) {
            if (!$this->translatableManager->isTranslatable($entityClass)) {
                throw new FiltererException(
                    sprintf('Property "%s::$%s" is not association or mapped field.', $entityClass, $property)
                );
            }

            $joinAlias = $this->translatableManager->getTranslationsProperty();
            $this->translationJoiner->joinTranslation($qb, null, $joinAlias, true);

            $rootAlias = $joinAlias;
        }

        return sprintf('%s.%s %s :%s', $rootAlias, $property, $this->getConstraintExpression($field, $strictComparison), $field);
    }

    /**
     * @param string $field            Constraint field
     * @param bool   $strictComparison Whether to use strict comparison
     *
     * @return string
     */
    private function getConstraintExpression($field, $strictComparison)
    {
        if (preg_match('/From$/', $field)) {
            return '>=';
        }
        if (preg_match('/To$/', $field)) {
            return '<=';
        }

        return $strictComparison ? '=' : 'LIKE';
    }

    /**
     * @param string $entityClass Entity class
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadataInfo
     * @throws \Darvin\ContentBundle\Filterer\FiltererException
     */
    private function getDoctrineMetadata($entityClass)
    {
        if (!isset($this->doctrineMetadata[$entityClass])) {
            try {
                $this->doctrineMetadata[$entityClass] = $this->em->getClassMetadata($entityClass);
            } catch (MappingException $ex) {
                throw new FiltererException(sprintf('Unable to get Doctrine metadata for class "%s".', $entityClass));
            }
        }

        return $this->doctrineMetadata[$entityClass];
    }
}
