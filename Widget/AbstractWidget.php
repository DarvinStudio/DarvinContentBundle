<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Widget abstract implementation
 */
abstract class AbstractWidget implements WidgetInterface
{
    /**
     * @var array
     */
    private $resolvedOptions;

    /**
     * {@inheritdoc}
     */
    public function getResolvedOptions()
    {
        if (null === $this->resolvedOptions) {
            $resolver = new OptionsResolver();
            $this->configureOptions($resolver);
            $this->resolvedOptions = $resolver->resolve($this->getOptions());
        }

        return $this->resolvedOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getSluggableEntityClasses()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSluggableEntities()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return '%'.$this->getName().'%';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver Options resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {

    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
