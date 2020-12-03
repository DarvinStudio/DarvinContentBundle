<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Doctrine\Common\Util\ClassUtils;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Translation initializer
 */
class TranslationInitializer implements TranslationInitializerInterface
{
    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface
     */
    private $localeSetter;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface $localeSetter Translatable locale setter
     */
    public function __construct(TranslatableLocaleSetterInterface $localeSetter)
    {
        $this->localeSetter = $localeSetter;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeTranslations($entity, array $locales): void
    {
        if (!$entity instanceof TranslatableInterface) {
            throw new TranslatableException(sprintf('Class "%s" is not translatable.', ClassUtils::getClass($entity)));
        }

        $this->localeSetter->setLocales($entity);

        $translationClass = $entity::getTranslationEntityClass();

        foreach ($locales as $locale) {
            /** @var \Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface $translation */
            $translation = new $translationClass();
            $translation->setLocale($locale);
            $entity->addTranslation($translation);
        }
    }
}
