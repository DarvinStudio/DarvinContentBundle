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
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Rebuild slug map command
 */
class RebuildSlugMapCommand extends AbstractContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('darvin:content:rebuild-slug-map')
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
                $em->getEventManager()->dispatchEvent(Events::postPersist, new LifecycleEventArgs($entity, $em));

                $this->info($doctrineMeta->getName().' '.implode('', $doctrineMeta->getIdentifierValues($entity)));
            }
        }
    }

    private function truncateSlugMap()
    {
        $em = $this->getEntityManager();

        $tableName = $em->getClassMetadata(SlugMapItem::CLASS_NAME)->getTableName();

        $connection = $em->getConnection();
        $connection->executeQuery($connection->getDriver()->getDatabasePlatform()->getTruncateTableSQL($tableName));
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
}
