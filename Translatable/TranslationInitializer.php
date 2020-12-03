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
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface $localeSetter        Translatable locale setter
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface      $translatableManager Translatable manager
     */
    public function __construct(TranslatableLocaleSetterInterface $localeSetter, TranslatableManagerInterface $translatableManager)
    {
        $this->localeSetter = $localeSetter;
        $this->translatableManager = $translatableManager;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeTranslations($entity, array $locales): void
    {
        $class = ClassUtils::getClass($entity);

        if (!$this->translatableManager->isTranslatable($class)) {
            throw new TranslatableException(sprintf('Class "%s" is not translatable.', $class));
        }

        $this->localeSetter->setLocales($entity);

        /** @var \Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface $entity */
        $translationClass = $entity::getTranslationEntityClass();

        foreach ($locales as $locale) {
            /** @var \Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface $translation */
            $translation = new $translationClass();
            $translation->setLocale($locale);
            $entity->addTranslation($translation);
        }
    }
}
