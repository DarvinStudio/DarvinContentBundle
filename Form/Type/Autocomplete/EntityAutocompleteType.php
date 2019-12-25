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

use Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface;
use Darvin\Utils\Form\DataTransformer\EntityToIDTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Entity autocomplete form type
 */
class EntityAutocompleteType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface
     */
    private $providerConfig;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                                       $em             Entity manager
     * @param \Darvin\ContentBundle\Autocomplete\Provider\Config\ProviderConfigInterface $providerConfig Autocomplete provider configuration
     */
    public function __construct(EntityManagerInterface $em, ProviderConfigInterface $providerConfig)
    {
        $this->em = $em;
        $this->providerConfig = $providerConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new EntityToIDTransformer($this->em, $options['entity'], $options['multiple']));
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $providerConfig = $this->providerConfig;

        $resolver
            ->setDefault('entity', function (Options $options) use ($providerConfig): ?string {
                return $providerConfig->getProvider($options['provider'])->getOption('entity');
            })
            ->setAllowedTypes('entity', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): string
    {
        return AutocompleteType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_content_entity_autocomplete';
    }
}
