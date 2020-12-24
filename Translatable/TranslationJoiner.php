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
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

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
     * @param \Darvin\Utils\Locale\LocaleProviderInterface $localeProvider Locale provider
     */
    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
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

        if (!is_a($entityClass, TranslatableInterface::class, true)) {
            throw new TranslatableException(sprintf('Class "%s" is not translatable.', $entityClass));
        }

        $rootAliases = $qb->getRootAliases();
        $rootAlias = $rootAliases[0];

        if (null === $joinAlias) {
            $joinAlias = TranslatableManagerInterface::TRANSLATIONS_PROPERTY;
        }

        $join = implode('.', [$rootAlias, TranslatableManagerInterface::TRANSLATIONS_PROPERTY]);

        $sameAliasJoin = QueryBuilderUtil::findJoinByAlias($qb, $rootAlias, $joinAlias);

        if (null === $sameAliasJoin) {
            $inner ? $qb->innerJoin($join, $joinAlias) : $qb->leftJoin($join, $joinAlias);

            if ($addSelect) {
                $qb->addSelect($joinAlias);
            }
            if (null === $locale) {
                $locale = $this->localeProvider->getCurrentLocale();
            }

            $where = $qb->expr()->orX($joinAlias.sprintf('.%s = :%1$s', TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY));

            if (!$inner) {
                $where->add($joinAlias.sprintf('.%s IS NULL', TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY));
            }

            $qb
                ->andWhere($where)
                ->setParameter(TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY, $locale);

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
