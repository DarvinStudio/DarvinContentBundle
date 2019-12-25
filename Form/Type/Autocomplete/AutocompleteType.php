<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Form\Type\Autocomplete;

use Darvin\ContentBundle\Autocomplete\AutocompleterInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * Autocomplete form type
 */
class AutocompleteType extends AbstractType
{
    /**
     * @var \Darvin\ContentBundle\Autocomplete\AutocompleterInterface
     */
    private $autocompleter;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param \Darvin\ContentBundle\Autocomplete\AutocompleterInterface $autocompleter Autocompleter
     * @param \Symfony\Component\Routing\RouterInterface                $router        Router
     */
    public function __construct(AutocompleterInterface $autocompleter, RouterInterface $router)
    {
        $this->autocompleter = $autocompleter;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->resetViewTransformers();

        if (!$options['rebuild_choices']) {
            return;
        }

        $autocompleter = $this->autocompleter;
        $formType      = get_class($this);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($autocompleter, $builder, $formType, $options): void {
            $parentForm = $event->getForm()->getParent();

            if (null === $parentForm) {
                return;
            }

            $choices = [];
            $data    = $event->getData();

            if (null !== $data) {
                if (!is_array($data)) {
                    $data = [$data];
                }

                $choices = array_combine($data, $data);
            }

            $labels = $autocompleter->getChoiceLabels($options['provider'], $choices);

            $parentForm->add($builder->getName(), $formType, array_merge($options, [
                'choices'         => $choices,
                'rebuild_choices' => false,
                'choice_label'    => function ($choice) use ($labels) {
                    return $labels[$choice] ?? $choice;
                },
            ]));
        });
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['autocomplete_url'] = $this->router->generate('darvin_content_autocomplete', [
            'provider' => $options['provider'],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('provider')
            ->setDefault('rebuild_choices', true)
            ->setAllowedValues('provider', $this->autocompleter->getProviderNames())
            ->setAllowedTypes('rebuild_choices', 'bool');
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_content_autocomplete';
    }
}
