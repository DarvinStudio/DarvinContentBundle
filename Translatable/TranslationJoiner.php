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
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Translation joiner
 */
class TranslationJoiner implements TranslationJoinerInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack                  $requestStack        Request stack
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     */
    public function __construct(RequestStack $requestStack, TranslatableManagerInterface $translatableManager)
    {
        $this->requestStack = $requestStack;
        $this->translatableManager = $translatableManager;
    }

    /**
     * {@inheritdoc}
     */
    public function joinTranslation(QueryBuilder $qb, $locale = null, $joinAlias = null, $inner = false)
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

        if (empty($joinAlias)) {
            $joinAlias = $translationsProperty;
        }

        $join = $rootAlias.'.'.$translationsProperty;

        $sameAliasJoin = QueryBuilderUtil::findJoinByAlias($qb, $rootAlias, $joinAlias);

        if (empty($sameAliasJoin)) {
            $inner ? $qb->innerJoin($join, $joinAlias) : $qb->leftJoin($join, $joinAlias);

            if (empty($locale)) {
                $locale = $this->getLocaleFromRequest();
            }

            $qb
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

    /**
     * {@inheritdoc}
     */
    public function isTranslatable($entityClass)
    {
        return $this->translatableManager->isTranslatable($entityClass);
    }

    /**
     * @return string
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    private function getLocaleFromRequest()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (empty($request)) {
            throw new TranslatableException('Unable to get locale from current request: request is empty.');
        }

        return $request->getLocale();
    }
}
