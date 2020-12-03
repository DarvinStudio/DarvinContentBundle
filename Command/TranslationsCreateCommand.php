<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command;

use Darvin\ContentBundle\Translatable\TranslatableException;
use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Translations create command
 */
class TranslationsCreateCommand extends Command
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
     * @var string
     */
    private $defaultLocale;

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $io;

    /**
     * @param string                                                          $name                Command name
     * @param \Doctrine\ORM\EntityManagerInterface                            $em                  Entity manager
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     * @param string                                                          $defaultLocale       Default locale
     */
    public function __construct(string $name, EntityManagerInterface $em, TranslatableManagerInterface $translatableManager, string $defaultLocale)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->translatableManager = $translatableManager;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription(<<<EOF
Creates translations for all translatable entities and specified locale by cloning default locale translations.
EOF
            )
            ->setDefinition([
                new InputArgument('locale', InputArgument::REQUIRED),
            ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $targetLocale = $input->getArgument('locale');

        $classes = $this->getTranslationClasses();

        if (empty($classes)) {
            return 0;
        }

        $this->checkIfTargetLocaleTranslationsExist($classes, $targetLocale);
        $this->cloneDefaultLocaleTranslations($classes, $targetLocale);

        return 0;
    }

    /**
     * @param array  $translationClasses Translation classes
     * @param string $targetLocale       Target locale
     */
    private function cloneDefaultLocaleTranslations(array $translationClasses, string $targetLocale): void
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
                $this->io->comment($translationClass.' '.reset($ids));
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
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    private function checkIfTargetLocaleTranslationsExist(array $translationClasses, string $targetLocale): void
    {
        foreach ($translationClasses as $translationClass) {
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $this->em->getRepository($translationClass)->createQueryBuilder('o');

            $translationsCount = $qb
                ->select('COUNT(o)')
                ->where(sprintf('o.%s = :%1$s', TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY))
                ->setParameter(TranslatableManagerInterface::TRANSLATION_LOCALE_PROPERTY, $targetLocale)
                ->getQuery()
                ->getSingleScalarResult();

            if ($translationsCount > 0) {
                throw new TranslatableException(
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
            if ($this->translatableManager->isTranslatable($meta->getName())) {
                $class = $this->translatableManager->getTranslationClass($meta->getName());

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
