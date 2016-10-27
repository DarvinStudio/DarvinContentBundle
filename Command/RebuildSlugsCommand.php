<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command;

use Darvin\Utils\Sluggable\SluggableManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Rebuild slugs command
 */
class RebuildSlugsCommand extends Command
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Darvin\Utils\Sluggable\SluggableManagerInterface
     */
    private $sluggableManager;

    /**
     * @param string                                            $name             Command name
     * @param \Doctrine\ORM\EntityManager                       $em               Entity manager
     * @param \Darvin\Utils\Sluggable\SluggableManagerInterface $sluggableManager Sluggable manager
     */
    public function __construct($name, EntityManager $em, SluggableManagerInterface $sluggableManager)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->sluggableManager = $sluggableManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Rebuilds all slugs.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $meta */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $meta) {
            if ($meta->getReflectionClass()->isAbstract() || !$this->sluggableManager->isSluggable($meta->getName())) {
                continue;
            }
            foreach ($this->em->getRepository($meta->getName())->findAll() as $entity) {
                if ($this->sluggableManager->generateSlugs($entity, true)) {
                    $io->success(implode(' ', array_merge([$meta->getName()], $meta->getIdentifierValues($entity))));
                }
            }

            $this->em->flush();
        }
    }
}
