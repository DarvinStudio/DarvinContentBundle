<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Autocomplete\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Entity repository autocomplete provider
 */
class RepositoryAutocompleteProvider
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em Entity manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * @param string|null $term    Search term
     * @param array|null  $choices Choices
     * @param string      $locale  Locale
     * @param array       $options Options
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function __invoke(?string $term, ?array $choices, string $locale, array $options): array
    {
        $options = $this->optionsResolver->resolve($options);

        $entity = $options['entity'];
        $method = $options['repository_method'];

        $repository = $this->em->getRepository($entity);

        if (!method_exists($repository, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s()" does not exist.', get_class($repository), $method));
        }

        return $repository->$method($term, $choices, $locale);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver Options resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'entity',
                'repository_method',
            ])
            ->setAllowedTypes('entity', 'string')
            ->setAllowedTypes('repository_method', 'string');
    }
}
