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
    private $translationsProperty;

    /**
     * @var array
     */
    private $checkedObjectClasses;

    /**
     * @var array
     */
    private $translationClasses;

    /**
     * @param \Knp\DoctrineBehaviors\Reflection\ClassAnalyzer $classAnalyzer                   Class analyzer
     * @param \Doctrine\ORM\EntityManager                     $em                              Entity manager
     * @param string                                          $getTranslationEntityClassMethod Get translation entity class method name
     * @param bool                                            $isReflectionRecursive           Is reflection recursive
     * @param string                                          $translatableTrait               Translatable trait
     * @param string                                          $translationLocaleProperty       Translation locale property name
     * @param string                                          $translationsProperty            Translations property name
     */
    public function __construct(
        ClassAnalyzer $classAnalyzer,
        EntityManager $em,
        $getTranslationEntityClassMethod,
        $isReflectionRecursive,
        $translatableTrait,
        $translationLocaleProperty,
        $translationsProperty
    ) {
        $this->classAnalyzer = $classAnalyzer;
        $this->em = $em;
        $this->getTranslationEntityClassMethod = $getTranslationEntityClassMethod;
        $this->isReflectionRecursive = $isReflectionRecursive;
        $this->translatableTrait = $translatableTrait;
        $this->translationLocaleProperty = $translationLocaleProperty;
        $this->translationsProperty = $translationsProperty;
        $this->checkedObjectClasses = array();
        $this->translationClasses = array();
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

            $this->translationClasses[$entityClass] = call_user_func(array($entityClass, $this->getTranslationEntityClassMethod));
        }

        return $this->translationClasses[$entityClass];
    }

    /**
     * {@inheritdoc}
     */
    public function isTranslatable($entityClass)
    {
        if (!isset($this->checkedObjectClasses[$entityClass])) {
            $this->checkedObjectClasses[$entityClass] = $this->classAnalyzer->hasTrait(
                $this->getDoctrineMetadata($entityClass)->getReflectionClass(),
                $this->translatableTrait,
                $this->isReflectionRecursive
            );
        }

        return $this->checkedObjectClasses[$entityClass];
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
