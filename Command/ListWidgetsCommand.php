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

use Darvin\ContentBundle\Widget\WidgetPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * List widgets command
 */
class ListWidgetsCommand extends Command
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private $widgetPool;

    /**
     * @param string                                           $name         Command name
     * @param \Symfony\Component\HttpFoundation\RequestStack   $requestStack Request stack
     * @param \Darvin\ContentBundle\Widget\WidgetPoolInterface $widgetPool   Widget pool
     */
    public function __construct(string $name, RequestStack $requestStack, WidgetPoolInterface $widgetPool)
    {
        parent::__construct($name);

        $this->requestStack = $requestStack;
        $this->widgetPool = $widgetPool;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Displays list of existing content widget names.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->requestStack->push(new Request());

        $names = [];

        foreach ($this->widgetPool->getAllWidgets() as $widget) {
            $names[] = $widget->getName();
        }

        sort($names);

        (new SymfonyStyle($input, $output))->listing($names);

        return 0;
    }
}
