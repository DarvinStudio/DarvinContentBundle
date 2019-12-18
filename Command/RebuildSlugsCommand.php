<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command;

use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Darvin\Utils\Sluggable\SluggableManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \Darvin\Utils\Sluggable\SluggableManagerInterface
     */
    private $sluggableManager;

    /**
     * @param string                                                      $name             Command name
     * @param \Doctrine\ORM\EntityManager                                 $em               Entity manager
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface              $metadataFactory  Extended metadata factory
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     * @param \Darvin\Utils\Sluggable\SluggableManagerInterface           $sluggableManager Sluggable manager
     */
    public function __construct(
        string $name,
        EntityManager $em,
        MetadataFactoryInterface $metadataFactory,
        PropertyAccessorInterface $propertyAccessor,
        SluggableManagerInterface $sluggableManager
    ) {
        parent::__construct($name);

        $this->em = $em;
        $this->metadataFactory = $metadataFactory;
        $this->propertyAccessor = $propertyAccessor;
        $this->sluggableManager = $sluggableManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Rebuilds all slugs.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $meta */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $meta) {
            if ($meta->getReflectionClass()->isAbstract() || !$this->sluggableManager->isSluggable($meta->getName())) {
                continue;
            }

            $properties = array_keys($this->metadataFactory->getExtendedMetadata($meta->getName())['slugs']);

            foreach ($this->em->getRepository($meta->getName())->findAll() as $entity) {
                $comments = [];

                foreach ($properties as $property) {
                    // Reset slug
                    $this->propertyAccessor->setValue($entity, $property, uniqid((string)mt_rand(), true));
                }

                $this->em->flush();

                foreach ($properties as $property) {
                    $comments[] = $property.': '.$this->propertyAccessor->getValue($entity, $property);
                }

                $ids = $meta->getIdentifierValues($entity);

                $io->comment(implode(' ', [$meta->getName(), reset($ids), implode(', ', $comments)]));
            }
        }

        $this->em->flush();

        return 0;
    }
}
