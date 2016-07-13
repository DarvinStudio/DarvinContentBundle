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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Translations create command
 */
class TranslationsCreateCommand extends ContainerAwareCommand
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $io;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('darvin:content:translations:create')
            ->setDescription(<<<EOF
Creates translations for all translatable entities and specified locale by cloning default locale translations.
EOF
            )
            ->setDefinition(
                [
                new InputArgument('locale', InputArgument::REQUIRED),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $targetLocale = $input->getArgument('locale');

        $translationClasses = $this->getTranslationClasses();

        if (empty($translationClasses)) {
            return;
        }

        $this
            ->checkIfTargetLocaleTranslationsExist($translationClasses, $targetLocale)
            ->cloneDefaultLocaleTranslations($translationClasses, $targetLocale);
    }

    /**
     * @param array  $translationClasses Translation classes
     * @param string $targetLocale       Target locale
     *
     * @throws \Darvin\ContentBundle\Translatable\TranslatableException
     */
    private function cloneDefaultLocaleTranslations(array $translationClasses, $targetLocale)
    {
        $defaultLocale = $this->getContainer()->getParameter('locale');

        $em = $this->getEntityManager();

        $localeProperty = $this->getTranslatableManager()->getTranslationLocaleProperty();

        foreach ($translationClasses as $translationClass) {
            $defaultLocaleTranslations = $em->getRepository($translationClass)->findBy(
                [
                $localeProperty => $defaultLocale,
                ]
            );

            foreach ($defaultLocaleTranslations as $translation) {
                $doctrineMeta = $em->getClassMetadata($translationClass);

                $translationClone = clone $translation;
                $ids = $doctrineMeta->getIdentifier();
                $doctrineMeta->setIdentifierValues($translationClone, array_fill_keys($ids, null));
                $doctrineMeta->setFieldValue($translationClone, $localeProperty, $targetLocale);
                $em->persist($translationClone);

                $ids = $doctrineMeta->getIdentifierValues($translation);
                $this->io->comment($translationClass.' '.reset($ids));
            }

            $em->flush();
            $em->clear();
        }
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
        $em = $this->getEntityManager();

        $localeProperty = $this->getTranslatableManager()->getTranslationLocaleProperty();

        foreach ($translationClasses as $translationClass) {
            $translationsCount = $em->getRepository($translationClass)->createQueryBuilder('o')
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
        $translationClasses = [];

        $translatableManager = $this->getTranslatableManager();

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $doctrineMeta */
        foreach ($this->getEntityManager()->getMetadataFactory()->getAllMetadata() as $doctrineMeta) {
            if ($translatableManager->isTranslatable($doctrineMeta->getName())) {
                $translationClasses[] = $translatableManager->getTranslationClass($doctrineMeta->getName());
            }
        }

        return $translationClasses;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private function getTranslatableManager()
    {
        return $this->getContainer()->get('darvin_content.translatable.manager');
    }
}
