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
    private const GET_TRANSLATABLE_ENTITY_CLASS_METHOD = 'getTranslatableEntityClass';
    private const GET_TRANSLATION_ENTITY_CLASS_METHOD  = 'getTranslationEntityClass';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $translatableClasses;

    /**
     * @var array
     */
    private $translationClasses;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em Entity manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatableClass(string $entityClass): string
    {
        if (!isset($this->translatableClasses[$entityClass])) {
            if (!is_a($entityClass, TranslationInterface::class, true)) {
                throw new TranslatableException(sprintf('Class "%s" is not translation.', $entityClass));
            }

            $this->translatableClasses[$entityClass] = call_user_func(
                [$entityClass, self::GET_TRANSLATABLE_ENTITY_CLASS_METHOD]
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
            if (!is_a($entityClass, TranslatableInterface::class, true)) {
                throw new TranslatableException(sprintf('Class "%s" is not translatable.', $entityClass));
            }

            $this->translationClasses[$entityClass] = call_user_func(
                [$this->getDoctrineMetadata($entityClass)->getName(), self::GET_TRANSLATION_ENTITY_CLASS_METHOD]
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
        return self::TRANSLATION_LOCALE_PROPERTY;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslationsProperty(): string
    {
        return self::TRANSLATIONS_PROPERTY;
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
