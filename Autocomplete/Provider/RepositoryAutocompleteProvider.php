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

use Doctrine\Persistence\ObjectManager;

/**
 * Object repository autocomplete provider
 */
class RepositoryAutocompleteProvider
{
    /**
     * @var \Doctrine\Persistence\ObjectManager
     */
    private $om;

    /**
     * @param \Doctrine\Persistence\ObjectManager $om Object manager
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param string|null $term             Search term
     * @param array|null  $choices          Choices
     * @param string      $locale           Locale
     * @param string      $objectClass      Object class
     * @param string      $repositoryMethod Object repository method
     *
     * @return array
     */
    public function __invoke(?string $term, ?array $choices, string $locale, string $objectClass, string $repositoryMethod): array
    {
        $repository = $this->om->getRepository($objectClass);

        if (!method_exists($repository, $repositoryMethod)) {
            throw new \InvalidArgumentException(
                sprintf('Method "%s::%s()" does not exist.', get_class($repository), $repositoryMethod)
            );
        }

        return $repository->$repositoryMethod($term, $choices, $locale);
    }
}
