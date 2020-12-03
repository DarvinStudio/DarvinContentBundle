<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Darvin\Utils\Locale\LocaleProviderInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Translatable locale setter
 */
class TranslatableLocaleSetter implements TranslatableLocaleSetterInterface
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
    public function setLocales(TranslatableInterface $translatable): TranslatableInterface
    {
        $translatable->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $translatable->setDefaultLocale($this->localeProvider->getDefaultLocale());

        return $translatable;
    }
}
