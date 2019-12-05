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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Knp\DoctrineBehaviors\ORM\Translatable\TranslatableSubscriber;

/**
 * Translation initializer
 */
class TranslationInitializer implements TranslationInitializerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @var \Knp\DoctrineBehaviors\ORM\Translatable\TranslatableSubscriber
     */
    private $translatableSubscriber;

    /**
     * @param \Doctrine\ORM\EntityManager                                     $em                     Entity manager
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager    Translatable manager
     * @param \Knp\DoctrineBehaviors\ORM\Translatable\TranslatableSubscriber  $translatableSubscriber Translatable subscriber
     */
    public function __construct(
        EntityManager $em,
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

        /** @var \Knp\DoctrineBehaviors\Model\Translatable\Translatable $entity */
        $translationClass = $entity::getTranslationEntityClass();

        foreach ($locales as $locale) {
            /** @var \Knp\DoctrineBehaviors\Model\Translatable\Translation $translation */
            $translation = new $translationClass();
            $translation->setLocale($locale);
            $entity->addTranslation($translation);
        }
    }
}
