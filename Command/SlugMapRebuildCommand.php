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

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Slug\SlugMapItemFactoryInterface;
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Slug map rebuild command
 */
class SlugMapRebuildCommand extends Command
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \Darvin\ContentBundle\Slug\SlugMapItemFactoryInterface
     */
    private $slugMapItemFactory;

    /**
     * @param string                                                 $name               Command name
     * @param \Doctrine\ORM\EntityManager                            $em                 Entity manager
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface         $metadataFactory    Metadata factory
     * @param \Darvin\ContentBundle\Slug\SlugMapItemFactoryInterface $slugMapItemFactory Slug map item factory
     */
    public function __construct(string $name, EntityManager $em, MetadataFactoryInterface $metadataFactory, SlugMapItemFactoryInterface $slugMapItemFactory)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->metadataFactory = $metadataFactory;
        $this->slugMapItemFactory = $slugMapItemFactory;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Rebuilds slug map.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->truncateSlugMap();

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
                foreach ($this->slugMapItemFactory->createItems($entity, $extendedMeta['slugs'], $doctrineMeta) as $slugMapItem) {
                    $this->em->persist($slugMapItem);
                }

                $io->comment($doctrineMeta->getName().' '.implode('', $doctrineMeta->getIdentifierValues($entity)));
            }
        }

        $this->em->flush();
    }

    private function truncateSlugMap(): void
    {
        $tableName = $this->em->getClassMetadata(SlugMapItem::class)->getTableName();

        $connection = $this->em->getConnection();
        $connection->exec('SET foreign_key_checks = 0');
        $connection->exec($connection->getDriver()->getDatabasePlatform()->getTruncateTableSQL($tableName));
    }
}
