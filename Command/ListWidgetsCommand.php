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
    public function __construct($name, RequestStack $requestStack, WidgetPoolInterface $widgetPool)
    {
        parent::__construct($name);

        $this->requestStack = $requestStack;
        $this->widgetPool = $widgetPool;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Displays list of existing content widget placeholders.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->requestStack->push(new Request());

        $placeholders = array_keys($this->widgetPool->getAllWidgets());
        sort($placeholders);

        (new SymfonyStyle($input, $output))->listing($placeholders);
    }
}
