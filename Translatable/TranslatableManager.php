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
use Doctrine\Common\Persistence\ObjectManager;
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
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var bool
     */
    private $isReflectionRecursive;

    /**
     * @var string
     */
    private $translatableTrait;

    /**
     * @var array
     */
    private $checkedObjectClasses;

    /**
     * @var array
     */
    private $translationClasses;

    /**
     * @param \Knp\DoctrineBehaviors\Reflection\ClassAnalyzer $classAnalyzer         Class analyzer
     * @param \Doctrine\Common\Persistence\ObjectManager      $om                    Object manager
     * @param bool                                            $isReflectionRecursive Is reflection recursive
     * @param string                                          $translatableTrait     Translatable trait
     */
    public function __construct(ClassAnalyzer $classAnalyzer, ObjectManager $om, $isReflectionRecursive, $translatableTrait)
    {
        $this->classAnalyzer = $classAnalyzer;
        $this->om = $om;
        $this->isReflectionRecursive = $isReflectionRecursive;
        $this->translatableTrait = $translatableTrait;
        $this->checkedObjectClasses = array();
        $this->translationClasses = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationClass($objectClass)
    {
        if (!isset($this->translationClasses[$objectClass])) {
            if (!$this->isTranslatable($objectClass)) {
                throw new TranslatableException(sprintf('Class "%s" is not translatable.', $objectClass));
            }

            $this->translationClasses[$objectClass] = call_user_func(array($objectClass, 'getTranslationEntityClass'));
        }

        return $this->translationClasses[$objectClass];
    }

    /**
     * {@inheritdoc}
     */
    public function isTranslatable($objectClass)
    {
        if (!isset($this->checkedObjectClasses[$objectClass])) {
            $this->checkedObjectClasses[$objectClass] = $this->classAnalyzer->hasTrait(
                $this->getDoctrineMetadata($objectClass)->getReflectionClass(),
                $this->translatableTrait,
                $this->isReflectionRecursive
            );
        }

        return $this->checkedObjectClasses[$objectClass];
    }

    /**
     * @param string $objectClass Object class
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    private function getDoctrineMetadata($objectClass)
    {
        try {
            return $this->om->getClassMetadata($objectClass);
        } catch (MappingException $ex) {
            throw new TranslatableException(sprintf('Unable to get Doctrine metadata for class "%s".', $objectClass));
        }
    }
}
