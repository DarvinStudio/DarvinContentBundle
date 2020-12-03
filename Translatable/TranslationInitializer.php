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

use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

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
     * @var string[]
     */
    private $defaultLocales;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface $localeSetter   Translatable locale setter
     * @param string[]                                                             $defaultLocales Default locales
     */
    public function __construct(TranslatableLocaleSetterInterface $localeSetter, array $defaultLocales)
    {
        $this->localeSetter = $localeSetter;
        $this->defaultLocales = $defaultLocales;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeTranslations(TranslatableInterface $translatable, ?array $locales = null): void
    {
        if (null === $locales) {
            $locales = $this->defaultLocales;
        }

        $this->localeSetter->setLocales($translatable);

        $translationClass = $translatable::getTranslationEntityClass();

        foreach ($locales as $locale) {
            $translatable->addTranslation($this->createTranslation($translationClass, $locale));
        }
    }

    /**
     * @param string $class  Translation class
     * @param string $locale Locale
     *
     * @return \Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface
     */
    private function createTranslation(string $class, string $locale): TranslationInterface
    {
        $translation = new $class();
        $translation->setLocale($locale);

        return $translation;
    }
}
