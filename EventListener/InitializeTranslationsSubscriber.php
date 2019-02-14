<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Darvin\ContentBundle\Translatable\TranslationInitializerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Initialize translations event subscriber
 */
class InitializeTranslationsSubscriber implements EventSubscriber
{
    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslationInitializerInterface
     */
    private $translationInitializer;

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface    $translatableManager    Translatable manager
     * @param \Darvin\ContentBundle\Translatable\TranslationInitializerInterface $translationInitializer Translation initializer
     * @param string[]                                                           $locales                Locales
     */
    public function __construct(TranslatableManagerInterface $translatableManager, TranslationInitializerInterface $translationInitializer, array $locales)
    {
        $this->translatableManager = $translatableManager;
        $this->translationInitializer = $translationInitializer;
        $this->locales = $locales;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args Event arguments
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();

        foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            if ($this->translatableManager->isTranslatable(ClassUtils::getClass($entity))) {
                $this->initializeTranslations($em, $entity);
            }
        }
    }

    /**
     * @param \Doctrine\ORM\EntityManager                    $em     Entity manager
     * @param \Darvin\ContentBundle\Traits\TranslatableTrait $entity Entity
     */
    private function initializeTranslations(EntityManager $em, $entity): void
    {
        $locales = [];

        foreach ($this->locales as $locale) {
            /** @var \Darvin\ContentBundle\Traits\TranslationTrait $translation */
            foreach ($entity->getTranslations() as $translation) {
                if ($translation->getLocale() === $locale) {
                    continue 2;
                }
            }
            foreach ($entity->getNewTranslations() as $translation) {
                if ($translation->getLocale() === $locale) {
                    continue 2;
                }
            }

            $locales[] = $locale;
        }
        if (empty($locales)) {
            return;
        }

        $this->translationInitializer->initializeTranslations($entity, $locales);

        $meta = $em->getClassMetadata($entity::getTranslationEntityClass());

        foreach ($entity->getTranslations() as $translation) {
            if (null === $translation->getId()) {
                $em->persist($translation);
                $em->getUnitOfWork()->computeChangeSet($meta, $translation);
            }
        }
    }
}
