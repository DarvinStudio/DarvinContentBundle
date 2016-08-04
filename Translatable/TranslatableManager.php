<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Translatable;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;

/**
 * Translatable manager
 */
class TranslatableManager implements TranslatableManagerInterface
{
    /**
     * @var \Knp\DoctrineBehaviors\Reflection\ClassAnalyzer
     */
    private $classAnalyzer;

    /**
     * @var \Doctrine\ORM\EntityManager
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
     * @var bool
     */
    private $isReflectionRecursive;

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
    private $checkedIfTranslatable;

    /**
     * @var array
     */
    private $checkedIfTranslation;

    /**
     * @var array
     */
    private $translatableClasses;

    /**
     * @var array
     */
    private $translationClasses;

    /**
     * @param \Knp\DoctrineBehaviors\Reflection\ClassAnalyzer $classAnalyzer                    Class analyzer
     * @param \Doctrine\ORM\EntityManager                     $em                               Entity manager
     * @param string                                          $getTranslatableEntityClassMethod Get translatable entity class method name
     * @param string                                          $getTranslationEntityClassMethod  Get translation entity class method name
     * @param bool                                            $isReflectionRecursive            Is reflection recursive
     * @param string                                          $translatableTrait                Translatable trait
     * @param string                                          $translationLocaleProperty        Translation locale property name
     * @param string                                          $translationTrait                 Translation trait
     * @param string                                          $translationsProperty             Translations property name
     */
    public function __construct(
        ClassAnalyzer $classAnalyzer,
        EntityManager $em,
        $getTranslatableEntityClassMethod,
        $getTranslationEntityClassMethod,
        $isReflectionRecursive,
        $translatableTrait,
        $translationLocaleProperty,
        $translationTrait,
        $translationsProperty
    ) {
        $this->classAnalyzer = $classAnalyzer;
        $this->em = $em;
        $this->getTranslatableEntityClassMethod = $getTranslatableEntityClassMethod;
        $this->getTranslationEntityClassMethod = $getTranslationEntityClassMethod;
        $this->isReflectionRecursive = $isReflectionRecursive;
        $this->translatableTrait = $translatableTrait;
        $this->translationLocaleProperty = $translationLocaleProperty;
        $this->translationTrait = $translationTrait;
        $this->translationsProperty = $translationsProperty;
        $this->checkedIfTranslatable = $this->checkedIfTranslation = $this->translatableClasses = $this->translationClasses = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatableClass($entityClass)
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
     * {@inheritdoc}
     */
    public function getTranslationClass($entityClass)
    {
        if (!isset($this->translationClasses[$entityClass])) {
            if (!$this->isTranslatable($entityClass)) {
                throw new TranslatableException(sprintf('Class "%s" is not translatable.', $entityClass));
            }

            $this->translationClasses[$entityClass] = call_user_func(
                [$entityClass, $this->getTranslationEntityClassMethod]
            );
        }

        return $this->translationClasses[$entityClass];
    }

    /**
     * {@inheritdoc}
     */
    public function isTranslatable($entityClass)
    {
        if (!isset($this->checkedIfTranslatable[$entityClass])) {
            $this->checkedIfTranslatable[$entityClass] = $this->classAnalyzer->hasTrait(
                $this->getDoctrineMetadata($entityClass)->getReflectionClass(),
                $this->translatableTrait,
                $this->isReflectionRecursive
            );
        }

        return $this->checkedIfTranslatable[$entityClass];
    }

    /**
     * {@inheritdoc}
     */
    public function isTranslation($entityClass)
    {
        if (!isset($this->checkedIfTranslation[$entityClass])) {
            $this->checkedIfTranslation[$entityClass] = $this->classAnalyzer->hasTrait(
                $this->getDoctrineMetadata($entityClass)->getReflectionClass(),
                $this->translationTrait,
                $this->isReflectionRecursive
            );
        }

        return $this->checkedIfTranslation[$entityClass];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationLocaleProperty()
    {
        return $this->translationLocaleProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationsProperty()
    {
        return $this->translationsProperty;
    }

    /**
     * @param string $entityClass Entity class
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadataInfo
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    private function getDoctrineMetadata($entityClass)
    {
        try {
            return $this->em->getClassMetadata($entityClass);
        } catch (MappingException $ex) {
            throw new TranslatableException(sprintf('Unable to get Doctrine metadata for class "%s".', $entityClass));
        }
    }
}
