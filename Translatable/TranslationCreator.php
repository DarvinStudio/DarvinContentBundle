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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Translation creator
 */
class TranslationCreator implements TranslationCreatorInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em            Entity manager
     * @param string                               $defaultLocale Default locale
     */
    public function __construct(EntityManagerInterface $em, string $defaultLocale)
    {
        $this->em = $em;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritDoc}
     */
    public function createTranslations(string $targetLocale, ?callable $output = null): void
    {
        $classes = $this->getTranslationClasses();

        if (empty($classes)) {
            return;
        }
        if (null === $output) {
            $output = function ($message): void {
            };
        }

        $this->checkIfTargetLocaleTranslationsExist($classes, $targetLocale);
        $this->cloneDefaultLocaleTranslations($output, $classes, $targetLocale);
    }

    /**
     * @param callable $output             Output callback
     * @param array    $translationClasses Translation classes
     * @param string   $targetLocale       Target locale
     */
    private function cloneDefaultLocaleTranslations(callable $output, array $translationClasses, string $targetLocale): void
    {
        $this->em->getConnection()->beginTransaction();

        foreach ($translationClasses as $translationClass) {
            $defaultLocaleTranslations = $this->em->getRepository($translationClass)->findBy([
                TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY => $this->defaultLocale,
            ]);

            foreach ($defaultLocaleTranslations as $translation) {
                $meta = $this->em->getClassMetadata($translationClass);

                $translationClone = clone $translation;
                $ids = $meta->getIdentifier();
                $meta->setIdentifierValues($translationClone, array_fill_keys($ids, null));
                $meta->setFieldValue($translationClone, TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY, $targetLocale);

                foreach ($meta->getAssociationNames() as $property) {
                    if ('translatable' !== $property) {
                        $meta->setFieldValue(
                            $translationClone,
                            $property,
                            $meta->isCollectionValuedAssociation($property) ? new ArrayCollection() : null
                        );
                    }
                }

                $this->em->persist($translationClone);

                $ids = $meta->getIdentifierValues($translation);
                $output($translationClass.' '.reset($ids));
            }

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->getConnection()->commit();
    }

    /**
     * @param array  $translationClasses Translation classes
     * @param string $targetLocale       Target locale
     *
     * @throws \RuntimeException
     */
    private function checkIfTargetLocaleTranslationsExist(array $translationClasses, string $targetLocale): void
    {
        foreach ($translationClasses as $translationClass) {
            $qb = $this->em->getRepository($translationClass)->createQueryBuilder('o');

            $translationsCount = $qb
                ->select('COUNT(o)')
                ->where(sprintf('o.%s = :%1$s', TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY))
                ->setParameter(TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY, $targetLocale)
                ->getQuery()
                ->getSingleScalarResult();

            if ($translationsCount > 0) {
                throw new \RuntimeException(
                    sprintf('Translations for locale "%s" already exist in repository "%s".', $targetLocale, $translationClass)
                );
            }
        }
    }

    /**
     * @return array
     */
    private function getTranslationClasses(): array
    {
        $classes = [];

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $meta */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $meta) {
            if (is_a($meta->getName(), TranslatableInterface::class, true)) {
                /** @var \Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface $translatableClass */
                $translatableClass = $meta->getName();

                $class = $translatableClass::getTranslationEntityClass();

                $classes[$class] = $class;
            }
        }
        foreach ($classes as $class) {
            foreach (class_parents($class) as $parent) {
                unset($classes[$parent]);
            }
        }

        return $classes;
    }
}
