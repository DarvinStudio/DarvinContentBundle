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

use Darvin\ContentBundle\Reference\ContentReferenceRebuilderInterface;
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
     * @var \Darvin\ContentBundle\Reference\ContentReferenceRebuilderInterface
     */
    private $rebuilder;

    /**
     * @param \Darvin\ContentBundle\Reference\ContentReferenceRebuilderInterface $rebuilder Content reference rebuilder
     */
    public function __construct(ContentReferenceRebuilderInterface $rebuilder)
    {
        parent::__construct();

        $this->rebuilder = $rebuilder;
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

        $this->rebuilder->rebuildContentReferences(function ($message) use ($io): void {
            $io->writeln($message);
        });

        return 0;
    }
}
