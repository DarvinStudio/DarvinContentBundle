<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Mapping\MappingException;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

/**
 * Translatable manager
 */
class TranslatableManager implements TranslatableManagerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $getTranslatableEntityClassMethod;

    /**
     * @var string
     */
    private $getTranslationEntityClassMethod;

    /**
     * @var string
     */
    private $translatableTrait;

    /**
     * @var string
     */
    private $translationLocaleProperty;

    /**
     * @var string
     */
    private $translationTrait;

    /**
     * @var string
     */
    private $translationsProperty;

    /**
     * @var array
     */
    private $translatableClasses;

    /**
     * @var array
     */
    private $translationClasses;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em                               Entity manager
     * @param string                               $getTranslatableEntityClassMethod Get translatable entity class method name
     * @param string                               $getTranslationEntityClassMethod  Get translation entity class method name
     * @param string                               $translatableTrait                Translatable trait
     * @param string                               $translationLocaleProperty        Translation locale property name
     * @param string                               $translationTrait                 Translation trait
     * @param string                               $translationsProperty             Translations property name
     */
    public function __construct(
        EntityManagerInterface $em,
        string $getTranslatableEntityClassMethod,
        string $getTranslationEntityClassMethod,
        string $translatableTrait,
        string $translationLocaleProperty,
        string $translationTrait,
        string $translationsProperty
    ) {
        $this->em = $em;
        $this->getTranslatableEntityClassMethod = $getTranslatableEntityClassMethod;
        $this->getTranslationEntityClassMethod = $getTranslationEntityClassMethod;
        $this->translatableTrait = $translatableTrait;
        $this->translationLocaleProperty = $translationLocaleProperty;
        $this->translationTrait = $translationTrait;
        $this->translationsProperty = $translationsProperty;
        $this->translatableClasses = $this->translationClasses = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatableClass(string $entityClass): string
    {
        if (!isset($this->translatableClasses[$entityClass])) {
            if (!$this->isTranslation($entityClass)) {
                throw new TranslatableException(sprintf('Class "%s" is not translation.', $entityClass));
            }

            $this->translatableClasses[$entityClass] = call_user_func(
                [$entityClass, $this->getTranslatableEntityClassMethod]
            );
        }

        return $this->translatableClasses[$entityClass];
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslationClass(string $entityClass): string
    {
        if (!isset($this->translationClasses[$entityClass])) {
            if (!$this->isTranslatable($entityClass)) {
                throw new TranslatableException(sprintf('Class "%s" is not translatable.', $entityClass));
            }

            $this->translationClasses[$entityClass] = call_user_func(
                [$this->getDoctrineMetadata($entityClass)->getName(), $this->getTranslationEntityClassMethod]
            );
        }

        return $this->translationClasses[$entityClass];
    }

    /**
     * {@inheritDoc}
     */
    public function isTranslatable(string $entityClass): bool
    {
        return in_array(TranslatableInterface::class, class_implements($entityClass));
    }

    /**
     * {@inheritDoc}
     */
    public function isTranslation(string $entityClass): bool
    {
        return in_array(TranslationInterface::class, class_implements($entityClass));
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslationLocaleProperty(): string
    {
        return $this->translationLocaleProperty;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslationsProperty(): string
    {
        return $this->translationsProperty;
    }

    /**
     * @param string $class Class
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadataInfo|null
     */
    private function getDoctrineMetadata(string $class): ?ClassMetadataInfo
    {
        try {
            return $this->em->getClassMetadata($class);
        } catch (MappingException $ex) {
            return null;
        }
    }
}
