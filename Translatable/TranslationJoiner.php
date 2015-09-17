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
    public function joinTranslation(QueryBuilder $qb, $locale)
    {
        $rootEntityClasses = $qb->getRootEntities();

        if (count($rootEntityClasses) > 1) {
            throw new TranslatableException(
                sprintf('Translation joiner "%s" supports only single root entity query builders.', __CLASS__)
            );
        }

        $entityClass = $rootEntityClasses[0];

        if (!$this->translatableManager->isTranslatable($entityClass)) {
            return;
        }

        $rootAliases = $qb->getRootAliases();
        $rootAlias = $rootAliases[0];

        $translationLocaleProperty = $this->translatableManager->getTranslationLocaleProperty();
        $translationsProperty = $this->translatableManager->getTranslationsProperty();

        $qb
            ->addSelect($translationsProperty)
            ->leftJoin($rootAlias.'.'.$translationsProperty, $translationsProperty)
            ->andWhere($translationsProperty.sprintf('.%s = :%1$s', $translationLocaleProperty))
            ->setParameter($translationLocaleProperty, $locale);
    }
}
