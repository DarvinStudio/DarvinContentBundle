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

use Darvin\ContentBundle\Entity\SlugMapItem;
use Darvin\ContentBundle\Slug\SlugMapItemFactory;
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
     * @var \Darvin\ContentBundle\Slug\SlugMapItemFactory
     */
    private $slugMapItemFactory;

    /**
     * @param string                                         $name               Command name
     * @param \Doctrine\ORM\EntityManager                    $em                 Entity manager
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface $metadataFactory    Metadata factory
     * @param \Darvin\ContentBundle\Slug\SlugMapItemFactory  $slugMapItemFactory Slug map item factory
     */
    public function __construct($name, EntityManager $em, MetadataFactoryInterface $metadataFactory, SlugMapItemFactory $slugMapItemFactory)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->metadataFactory = $metadataFactory;
        $this->slugMapItemFactory = $slugMapItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Rebuilds slug map.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->truncateSlugMap();

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $doctrineMeta */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $doctrineMeta) {
            if ($doctrineMeta->getReflectionClass()->isAbstract()) {
                continue;
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

    private function truncateSlugMap()
    {
        $tableName = $this->em->getClassMetadata(SlugMapItem::SLUG_MAP_ITEM_CLASS)->getTableName();

        $connection = $this->em->getConnection();
        $connection->exec($connection->getDriver()->getDatabasePlatform()->getTruncateTableSQL($tableName));
    }
}
