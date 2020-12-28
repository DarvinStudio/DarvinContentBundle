<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Command\Widget;

use Darvin\ContentBundle\Widget\WidgetRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Widget list command
 */
class ListCommand extends Command
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Darvin\ContentBundle\Widget\WidgetRegistryInterface
     */
    private $widgetRegistry;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack       $requestStack   Request stack
     * @param \Darvin\ContentBundle\Widget\WidgetRegistryInterface $widgetRegistry Widget registry
     */
    public function __construct(RequestStack $requestStack, WidgetRegistryInterface $widgetRegistry)
    {
        parent::__construct();

        $this->requestStack = $requestStack;
        $this->widgetRegistry = $widgetRegistry;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('darvin:content:widget:list')
            ->setDescription('Displays list of existing content widget names.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->requestStack->push(new Request());

        $names = [];

        foreach ($this->widgetRegistry->getAllWidgets() as $widget) {
            $names[] = $widget->getName();
        }

        sort($names);

        (new SymfonyStyle($input, $output))->listing($names);

        return 0;
    }
}
