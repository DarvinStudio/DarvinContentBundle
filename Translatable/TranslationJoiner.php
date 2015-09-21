<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Darvin\Utils\Doctrine\ORM\QueryBuilderUtil;
use Doctrine\ORM\QueryBuilder;

/**
 * Translation joiner
 */
class TranslationJoiner implements TranslationJoinerInterface
{
    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     */
    public function __construct(TranslatableManagerInterface $translatableManager)
    {
        $this->translatableManager = $translatableManager;
    }

    /**
     * {@inheritdoc}
     */
    public function joinTranslation(QueryBuilder $qb, $locale, $joinAlias = null)
    {
        $rootEntities = $qb->getRootEntities();

        if (count($rootEntities) > 1) {
            throw new TranslatableException('Only single root entity query builders are supported.');
        }

        $entityClass = $rootEntities[0];

        if (!$this->translatableManager->isTranslatable($entityClass)) {
            throw new TranslatableException(sprintf('Entity class "%s" is not translatable.', $entityClass));
        }

        $rootAliases = $qb->getRootAliases();
        $rootAlias = $rootAliases[0];

        $translationLocaleProperty = $this->translatableManager->getTranslationLocaleProperty();
        $translationsProperty = $this->translatableManager->getTranslationsProperty();

        if (empty($joinAlias)) {
            $joinAlias = $translationsProperty;
        }

        $join = $rootAlias.'.'.$translationsProperty;

        $sameAliasJoin = QueryBuilderUtil::findJoinByAlias($qb, $rootAlias, $joinAlias);

        if (empty($sameAliasJoin)) {
            $qb
                ->leftJoin($join, $joinAlias)
                ->andWhere($joinAlias.sprintf('.%s = :%1$s', $translationLocaleProperty))
                ->setParameter($translationLocaleProperty, $locale);

            return;
        }
        if ($join !== $sameAliasJoin->getJoin()) {
            $message = sprintf(
                'Unable to add join "%s" with alias "%s": expression with same alias already exists and has different join ("%s").',
                $join,
                $joinAlias,
                $sameAliasJoin->getJoin()
            );

            throw new TranslatableException($message);
        }
    }
}