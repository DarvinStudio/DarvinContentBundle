<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Reference;

use Darvin\ContentBundle\Entity\ContentReference;
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Content reference rebuilder
 */
class ContentReferenceRebuilder implements ContentReferenceRebuilderInterface
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
        $this->contentReferenceFactory = $contentReferenceFactory;
        $this->em = $em;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function rebuildContentReferences(?callable $output = null): void
    {
        if (null === $output) {
            $output = function ($message): void {
            };
        }

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

                $output(implode(' ', [$doctrineMeta->getName(), implode('', $doctrineMeta->getIdentifierValues($entity))]));
            }
        }

        $this->em->flush();
    }

    private function truncateReferenceTable(): void
    {
        $tableName = $this->em->getClassMetadata(ContentReference::class)->getTableName();

        $connection = $this->em->getConnection();
        $connection->executeStatement('SET foreign_key_checks = 0');
        $connection->executeStatement($connection->getDriver()->getDatabasePlatform()->getTruncateTableSQL($tableName));
    }
}
