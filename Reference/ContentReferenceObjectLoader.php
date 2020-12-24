<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Reference;

use Darvin\ContentBundle\Translatable\TranslationJoinerInterface;
use Darvin\ImageBundle\ORM\ImageJoinerInterface;
use Darvin\Utils\CustomObject\CustomObjectLoaderInterface;
use Darvin\Utils\Locale\LocaleProviderInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Content reference object loader
 */
class ContentReferenceObjectLoader implements ContentReferenceObjectLoaderInterface
{
    /**
     * @var \Darvin\Utils\CustomObject\CustomObjectLoaderInterface
     */
    private $genericCustomObjectLoader;

    /**
     * @var \Darvin\Utils\Locale\LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslationJoinerInterface
     */
    private $translationJoiner;

    /**
     * @var \Darvin\ImageBundle\ORM\ImageJoinerInterface|null
     */
    private $imageJoiner;

    /**
     * @param \Darvin\Utils\CustomObject\CustomObjectLoaderInterface        $genericCustomObjectLoader Generic custom object loader
     * @param \Darvin\Utils\Locale\LocaleProviderInterface                  $localeProvider            Locale provider
     * @param \Darvin\ContentBundle\Translatable\TranslationJoinerInterface $translationJoiner         Translation joiner
     */
    public function __construct(
        CustomObjectLoaderInterface $genericCustomObjectLoader,
        LocaleProviderInterface $localeProvider,
        TranslationJoinerInterface $translationJoiner
    ) {
        $this->genericCustomObjectLoader = $genericCustomObjectLoader;
        $this->localeProvider = $localeProvider;
        $this->translationJoiner = $translationJoiner;
    }

    /**
     * @param \Darvin\ImageBundle\ORM\ImageJoinerInterface|null $imageJoiner Image joiner
     */
    public function setImageJoiner(?ImageJoinerInterface $imageJoiner): void
    {
        $this->imageJoiner = $imageJoiner;
    }

    /**
     * {@inheritDoc}
     */
    public function loadObjects($references): void
    {
        if (empty($references)) {
            return;
        }

        $imageJoiner       = $this->imageJoiner;
        $locale            = $this->localeProvider->getCurrentLocale();
        $translationJoiner = $this->translationJoiner;

        $this->genericCustomObjectLoader->loadCustomObjects($references, function (QueryBuilder $qb) use ($locale, $imageJoiner, $translationJoiner) {
            if (null !== $imageJoiner) {
                $imageJoiner->joinImages($qb, $locale);
            }
            if (is_a($qb->getRootEntities()[0], TranslatableInterface::class, true)) {
                $translationJoiner->joinTranslation($qb, true, $locale, null, true);
            }
        });
    }
}
