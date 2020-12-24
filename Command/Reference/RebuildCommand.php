<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command\Reference;

use Darvin\ContentBundle\Entity\ContentReference;
use Darvin\ContentBundle\Reference\ContentReferenceFactoryInterface;
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Content reference rebuild command
 */
class RebuildCommand extends Command
{
    /**
     * @var \Darvin\ContentBundle\Reference\ContentReferenceFactoryInterface
     */
    private $contentReferenceFactory;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @param \Darvin\ContentBundle\Reference\ContentReferenceFactoryInterface $contentReferenceFactory Content reference factory
     * @param \Doctrine\ORM\EntityManagerInterface                             $em                      Entity manager
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface                   $metadataFactory         Metadata factory
     */
    public function __construct(
        ContentReferenceFactoryInterface $contentReferenceFactory,
        EntityManagerInterface $em,
        MetadataFactoryInterface $metadataFactory
    ) {
        parent::__construct();

        $this->contentReferenceFactory = $contentReferenceFactory;
        $this->em = $em;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('darvin:content:reference:rebuild')
            ->setDescription('Rebuilds content references.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->truncateReferenceTable();

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo[] $allDoctrineMeta */
        $allDoctrineMeta = $this->em->getMetadataFactory()->getAllMetadata();

        foreach ($allDoctrineMeta as $doctrineMeta) {
            if ($doctrineMeta->getReflectionClass()->isAbstract()) {
                continue;
            }
            foreach ($allDoctrineMeta as $otherDoctrineMeta) {
                if (in_array($doctrineMeta->getName(), class_parents($otherDoctrineMeta->getName()))) {
                    continue 2;
                }
            }

            $extendedMeta = $this->metadataFactory->getExtendedMetadata($doctrineMeta->getName());

            if (!isset($extendedMeta['slugs']) || empty($extendedMeta['slugs'])) {
                continue;
            }

            $entities = $this->em->getRepository($doctrineMeta->getName())->findAll();

            foreach ($entities as $entity) {
                foreach ($this->contentReferenceFactory->createContentReferences($entity, $extendedMeta['slugs'], $doctrineMeta) as $reference) {
                    $this->em->persist($reference);
                }

                $io->comment($doctrineMeta->getName().' '.implode('', $doctrineMeta->getIdentifierValues($entity)));
            }
        }

        $this->em->flush();

        return 0;
    }

    private function truncateReferenceTable(): void
    {
        $tableName = $this->em->getClassMetadata(ContentReference::class)->getTableName();

        $connection = $this->em->getConnection();
        $connection->executeStatement('SET foreign_key_checks = 0');
        $connection->executeStatement($connection->getDriver()->getDatabasePlatform()->getTruncateTableSQL($tableName));
    }
}
