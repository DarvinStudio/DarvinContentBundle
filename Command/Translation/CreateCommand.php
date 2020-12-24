<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command\Translation;

use Darvin\ContentBundle\Translatable\TranslationCreatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Translation create command
 */
class CreateCommand extends Command
{
    /**
     * @var \Darvin\ContentBundle\Translatable\TranslationCreatorInterface
     */
    private $creator;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslationCreatorInterface $creator Translation creator
     */
    public function __construct(TranslationCreatorInterface $creator)
    {
        parent::__construct();

        $this->creator = $creator;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('darvin:content:translation:create')
            ->setDescription(<<<EOF
Creates translations for all translatable entities and specified locale by cloning default locale translations.
EOF
            )
            ->setDefinition([
                new InputArgument('locale', InputArgument::REQUIRED),
            ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->creator->createTranslations($input->getArgument('locale'), function ($message) use ($io): void {
            $io->writeln($message);
        });

        return 0;
    }
}
