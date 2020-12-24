<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command\Slug;

use Darvin\ContentBundle\Slug\SlugRebuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Slug rebuild command
 */
class RebuildCommand extends Command
{
    /**
     * @var \Darvin\ContentBundle\Slug\SlugRebuilderInterface
     */
    private $rebuilder;

    /**
     * @param \Darvin\ContentBundle\Slug\SlugRebuilderInterface $rebuilder Slug rebuilder
     */
    public function __construct(SlugRebuilderInterface $rebuilder)
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
            ->setName('darvin:content:slug:rebuild')
            ->setDescription('Rebuilds all slugs.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->rebuilder->rebuildSlugs(function ($message) use ($io): void {
            $io->writeln($message);
        });

        return 0;
    }
}
