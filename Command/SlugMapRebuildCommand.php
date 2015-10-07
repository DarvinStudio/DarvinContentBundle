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
use Darvin\Utils\Command\AbstractContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Slug map rebuild command
 */
class SlugMapRebuildCommand extends AbstractContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('darvin:content:slug-map:rebuild')
            ->setDescription('Rebuilds slug map.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->truncateSlugMap();

        $em = $this->getEntityManager();
        $darvinMetadataFactory = $this->getDarvinMetadataFactory();
        $slugMapItemFactory = $this->getSlugMapItemFactory();

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $doctrineMeta */
        foreach ($em->getMetadataFactory()->getAllMetadata() as $doctrineMeta) {
            if ($doctrineMeta->getReflectionClass()->isAbstract()) {
                continue;
            }

            $darvinMeta = $darvinMetadataFactory->getMetadata($doctrineMeta);

            if (!isset($darvinMeta['slugs']) || empty($darvinMeta['slugs'])) {
                continue;
            }

            $entities = $em->getRepository($doctrineMeta->getName())->findAll();

            foreach ($entities as $entity) {
                foreach ($slugMapItemFactory->createItems($entity, $darvinMeta['slugs'], $doctrineMeta) as $slugMapItem) {
                    $em->persist($slugMapItem);
                }

                $this->info($doctrineMeta->getName().' '.implode('', $doctrineMeta->getIdentifierValues($entity)));
            }
        }

        $em->flush();
    }

    private function truncateSlugMap()
    {
        $em = $this->getEntityManager();

        $tableName = $em->getClassMetadata(SlugMapItem::SLUG_MAP_ITEM_CLASS)->getTableName();

        $connection = $em->getConnection();
        $connection->exec($connection->getDriver()->getDatabasePlatform()->getTruncateTableSQL($tableName));
    }

    /**
     * @return \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private function getDarvinMetadataFactory()
    {
        return $this->getContainer()->get('darvin_utils.mapping.metadata_factory');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Darvin\ContentBundle\Slug\SlugMapItemFactory
     */
    private function getSlugMapItemFactory()
    {
        return $this->getContainer()->get('darvin_content.slug.map_item_factory');
    }
}
