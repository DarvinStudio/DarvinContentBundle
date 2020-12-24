<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\ORM;

use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Darvin\ContentBundle\Translatable\TranslationJoinerInterface;
use Darvin\Utils\ORM\QueryBuilderUtil;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\MappingException;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Sort entity joiner
 */
class SortEntityJoiner implements SortEntityJoinerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslationJoinerInterface
     */
    private $translationJoiner;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                          $em                Entity manager
     * @param \Darvin\ContentBundle\Translatable\TranslationJoinerInterface $translationJoiner Translation joiner
     */
    public function __construct(EntityManagerInterface $em, TranslationJoinerInterface $translationJoiner)
    {
        $this->em = $em;
        $this->translationJoiner = $translationJoiner;
    }

    /**
     * {@inheritDoc}
     */
    public function joinEntity(QueryBuilder $qb, ?string $sortPropertyPath, string $locale): void
    {
        $sortPropertyPath = (string)$sortPropertyPath;

        if ('' === $sortPropertyPath) {
            return;
        }

        $rootEntities = $qb->getRootEntities();

        if (count($rootEntities) > 1) {
            throw new \InvalidArgumentException('Only single root entity query builders are supported.');
        }

        $entityClass = $rootEntities[0];

        try {
            $doctrineMeta = $this->em->getClassMetadata($entityClass);
        } catch (MappingException $ex) {
            throw new \InvalidArgumentException(sprintf('Unable to get Doctrine metadata for class "%s".', $entityClass));
        }

        $parts = explode('.', $sortPropertyPath);
        $partsCount = count($parts);

        if (!in_array($partsCount, [2, 3])) {
            throw new \InvalidArgumentException(sprintf('Property path must consist of 2 or 3 parts, %d provided.', $partsCount));
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
     * @throws \InvalidArgumentException
     */
    protected function joinBy2PartsPropertyPath(
        QueryBuilder $qb,
        array $propertyPathParts,
        string $locale,
        ClassMetadataInfo $doctrineMeta,
        string $qbRootAlias
    ): void {
        $firstPart = $propertyPathParts[0];

        if ('o' === $firstPart) {
            return;
        }
        if (is_a($doctrineMeta->getName(), TranslatableInterface::class, true)
            && $firstPart === TranslatableManagerInterface::TRANSLATIONS_PROPERTY
        ) {
            $this->translationJoiner->joinTranslation($qb, false, $locale);

            return;
        }
        if (!$doctrineMeta->hasAssociation($firstPart)) {
            return;
        }

        $join = implode('.', [$qbRootAlias, $firstPart]);

        $sameAliasJoin = QueryBuilderUtil::findJoinByAlias($qb, $qbRootAlias, $firstPart);

        if (null === $sameAliasJoin) {
            $qb->leftJoin($join, $firstPart);

            return;
        }
        if ($join !== $sameAliasJoin->getJoin()) {
            $message = sprintf(
                'Unable to add join "%s" with alias "%s": expression with same alias already exists and has different join ("%s").',
                $join,
                $firstPart,
                $sameAliasJoin->getJoin()
            );

            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder              $qb                Query builder
     * @param array                                   $propertyPathParts Property path parts
     * @param string                                  $locale            Locale
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $doctrineMeta      Doctrine metadata
     *
     * @throws \InvalidArgumentException
     */
    protected function joinBy3PartsPropertyPath(
        QueryBuilder $qb,
        array $propertyPathParts,
        string $locale,
        ClassMetadataInfo $doctrineMeta
    ): void {
        list($firstPart, $secondPart) = $propertyPathParts;

        if (!$doctrineMeta->hasAssociation($firstPart)) {
            return;
        }

        $associatedEntity = $doctrineMeta->associationMappings[$firstPart]['targetEntity'];

        if (is_subclass_of($doctrineMeta->getName(), $associatedEntity)) {
            $associatedEntity = $doctrineMeta->getName();
        }
        if (!is_a($associatedEntity, TranslatableInterface::class, true)) {
            $message = sprintf(
                'Entity class "%s" must be translatable in order to sort by 3 parts property path.',
                $associatedEntity
            );

            throw new \InvalidArgumentException($message);
        }
        if ($secondPart !== TranslatableManagerInterface::TRANSLATIONS_PROPERTY) {
            throw new \InvalidArgumentException(
                sprintf('Translations property must have name "%s", "%s" provided.', TranslatableManagerInterface::TRANSLATIONS_PROPERTY, $secondPart)
            );
        }

        $this->translationJoiner->joinTranslation($qb, false, $locale, $firstPart);
    }
}
