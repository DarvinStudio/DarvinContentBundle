<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Darvin\Utils\Locale\LocaleProviderInterface;
use Darvin\Utils\ORM\QueryBuilderUtil;
use Doctrine\ORM\QueryBuilder;

/**
 * Translation joiner
 */
class TranslationJoiner implements TranslationJoinerInterface
{
    /**
     * @var \Darvin\Utils\Locale\LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @param \Darvin\Utils\Locale\LocaleProviderInterface                    $localeProvider      Locale provider
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     */
    public function __construct(LocaleProviderInterface $localeProvider, TranslatableManagerInterface $translatableManager)
    {
        $this->localeProvider = $localeProvider;
        $this->translatableManager = $translatableManager;
    }

    /**
     * {@inheritDoc}
     */
    public function joinTranslation(QueryBuilder $qb, bool $addSelect = false, ?string $locale = null, ?string $joinAlias = null, bool $inner = false): void
    {
        $rootEntities = $qb->getRootEntities();

        if (count($rootEntities) > 1) {
            throw new TranslatableException('Only single root entity query builders are supported.');
        }

        $entityClass = $rootEntities[0];

        if (!$this->isTranslatable($entityClass)) {
            throw new TranslatableException(sprintf('Class "%s" is not translatable.', $entityClass));
        }

        $rootAliases = $qb->getRootAliases();
        $rootAlias = $rootAliases[0];

        $translationLocaleProperty = $this->translatableManager->getTranslationLocaleProperty();
        $translationsProperty = $this->translatableManager->getTranslationsProperty();

        if (null === $joinAlias) {
            $joinAlias = $translationsProperty;
        }

        $join = $rootAlias.'.'.$translationsProperty;

        $sameAliasJoin = QueryBuilderUtil::findJoinByAlias($qb, $rootAlias, $joinAlias);

        if (null === $sameAliasJoin) {
            $inner ? $qb->innerJoin($join, $joinAlias) : $qb->leftJoin($join, $joinAlias);

            if ($addSelect) {
                $qb->addSelect($joinAlias);
            }
            if (null === $locale) {
                $locale = $this->localeProvider->getCurrentLocale();
            }

            $where = $qb->expr()->orX($joinAlias.sprintf('.%s = :%1$s', $translationLocaleProperty));

            if (!$inner) {
                $where->add($joinAlias.sprintf('.%s IS NULL', $translationLocaleProperty));
            }

            $qb
                ->andWhere($where)
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

    /**
     * {@inheritDoc}
     */
    public function isTranslatable(string $entityClass): bool
    {
        return $this->translatableManager->isTranslatable($entityClass);
    }
}
