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
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

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
     * @param \Symfony\Component\HttpFoundation\RequestStack                  $requestStack        Request stack
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     * @param \Darvin\ContentBundle\Translatable\TranslationJoinerInterface   $translationJoiner   Translation joiner
     */
    public function __construct(
        EntityManager $em,
        RequestStack $requestStack,
        TranslatableManagerInterface $translatableManager,
        TranslationJoinerInterface $translationJoiner
    ) {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->translatableManager = $translatableManager;
        $this->translationJoiner = $translationJoiner;
        $this->optionsResolver = new OptionsResolver();
        $this->doctrineMetadata = array();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(QueryBuilder $qb, array $filterData = null, array $options = array())
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

        $options = $this->optionsResolver->resolve($options);

        $rootAlias = $rootAliases[0];

        $rootEntities = $qb->getRootEntities();
        $entityClass = $rootEntities[0];

        foreach ($filterData as $field => $value) {
            $strictComparison = !in_array($field, $options['non_strict_comparison_fields']);
            $this->addConstraint($qb, $field, $value, $entityClass, $rootAlias, $strictComparison);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver Options resolver
     */
    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'non_strict_comparison_fields' => array(),
            ))
            ->setAllowedTypes(array(
                'non_strict_comparison_fields' => 'array',
            ));
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb               Query builder
     * @param string                     $field            Constraint field
     * @param mixed                      $value            Constraint value
     * @param string                     $entityClass      Entity class
     * @param string                     $rootAlias        Query builder root alias
     * @param bool                       $strictComparison Whether to use strict comparison
     *
     * @throws \Darvin\ContentBundle\Filterer\FiltererException
     */
    private function addConstraint(QueryBuilder $qb, $field, $value, $entityClass, $rootAlias, $strictComparison)
    {
        $property = preg_replace('/(From|To)$/', '', $field);

        $doctrineMeta = $this->getDoctrineMetadata($entityClass);

        if (!isset($doctrineMeta->associationMappings[$property]) && !isset($doctrineMeta->fieldMappings[$property])) {
            if (!$this->translatableManager->isTranslatable($entityClass)) {
                throw new FiltererException(
                    sprintf('Property "%s::$%s" is not association or mapped field.', $entityClass, $property)
                );
            }

            $request = $this->requestStack->getCurrentRequest();

            if (empty($request)) {
                throw new FiltererException('Unable to get current locale: request is empty.');
            }

            $joinAlias = $this->translatableManager->getTranslationsProperty();
            $this->translationJoiner->joinTranslation($qb, $request->getLocale(), $joinAlias, true);

            $rootAlias = $joinAlias;
        }

        $qb
            ->andWhere(sprintf('%s.%s %s :%2$s', $rootAlias, $property, $this->getConstraintExpression($field, $strictComparison)))
            ->setParameter($property, $strictComparison ? $value : '%'.$value.'%');
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
