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

use Darvin\ContentBundle\EventListener\TranslatableSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Translation initializer
 */
class TranslationInitializer implements TranslationInitializerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @var \Darvin\ContentBundle\EventListener\TranslatableSubscriber
     */
    private $translatableSubscriber;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                            $em                     Entity manager
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager    Translatable manager
     * @param \Darvin\ContentBundle\EventListener\TranslatableSubscriber      $translatableSubscriber Translatable event subscriber
     */
    public function __construct(
        EntityManagerInterface $em,
        TranslatableManagerInterface $translatableManager,
        TranslatableSubscriber $translatableSubscriber
    ) {
        $this->em = $em;
        $this->translatableManager = $translatableManager;
        $this->translatableSubscriber = $translatableSubscriber;
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

        $this->translatableSubscriber->postLoad(new LifecycleEventArgs($entity, $this->em));

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
