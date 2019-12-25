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

use Symfony\Component\Form\AbstractType;

/**
 * Entity autocomplete form type
 */
class EntityAutocompleteType extends AbstractType
{
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
