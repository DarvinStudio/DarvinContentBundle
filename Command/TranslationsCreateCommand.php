<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command;

use Darvin\ContentBundle\Translatable\TranslatableException;
use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Doctrine\ORM\EntityManager;
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
     * @var \Doctrine\ORM\EntityManager
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
     * @param \Doctrine\ORM\EntityManager                                     $em                  Entity manager
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     * @param string                                                          $defaultLocale       Default locale
     */
    public function __construct($name, EntityManager $em, TranslatableManagerInterface $translatableManager, $defaultLocale)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->translatableManager = $translatableManager;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $targetLocale = $input->getArgument('locale');

        $classes = $this->getTranslationClasses();

        if (empty($classes)) {
            return;
        }

        $this
            ->checkIfTargetLocaleTranslationsExist($classes, $targetLocale)
            ->cloneDefaultLocaleTranslations($classes, $targetLocale);
    }

    /**
     * @param array  $translationClasses Translation classes
     * @param string $targetLocale       Target locale
     *
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    private function cloneDefaultLocaleTranslations(array $translationClasses, $targetLocale)
    {
        $this->em->getConnection()->beginTransaction();

        $localeProperty = $this->translatableManager->getTranslationLocaleProperty();

        foreach ($translationClasses as $translationClass) {
            $defaultLocaleTranslations = $this->em->getRepository($translationClass)->findBy([
                $localeProperty => $this->defaultLocale,
            ]);

            foreach ($defaultLocaleTranslations as $translation) {
                $doctrineMeta = $this->em->getClassMetadata($translationClass);

                $translationClone = clone $translation;
                $ids = $doctrineMeta->getIdentifier();
                $doctrineMeta->setIdentifierValues($translationClone, array_fill_keys($ids, null));
                $doctrineMeta->setFieldValue($translationClone, $localeProperty, $targetLocale);
                $this->em->persist($translationClone);

                $ids = $doctrineMeta->getIdentifierValues($translation);
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
     * @return TranslationsCreateCommand
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    private function checkIfTargetLocaleTranslationsExist(array $translationClasses, $targetLocale)
    {
        $localeProperty = $this->translatableManager->getTranslationLocaleProperty();

        foreach ($translationClasses as $translationClass) {
            $translationsCount = $this->em->getRepository($translationClass)->createQueryBuilder('o')
                ->select('COUNT(o)')
                ->where(sprintf('o.%s = :%1$s', $localeProperty))
                ->setParameter($localeProperty, $targetLocale)
                ->getQuery()
                ->getSingleScalarResult();

            if ($translationsCount > 0) {
                throw new TranslatableException(
                    sprintf('Translations for locale "%s" already exist in repository "%s".', $targetLocale, $translationClass)
                );
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    private function getTranslationClasses()
    {
        $classes = [];

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $meta */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $meta) {
            if ($this->translatableManager->isTranslatable($meta->getName())) {
                $class = $this->translatableManager->getTranslationClass($meta->getName());

                $classes[$class] = $class;
            }
        }

        $toRemove = [];

        foreach ($classes as $class) {
            foreach (class_parents($class) as $parent) {
                if (isset($classes[$parent])) {
                    $toRemove[] = $class;
                }
            }
        }
        foreach ($toRemove as $class) {
            unset($classes[$class]);
        }

        return $classes;
    }
}
