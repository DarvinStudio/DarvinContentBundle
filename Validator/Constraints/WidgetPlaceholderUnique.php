<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Widget placeholder unique validation constraint
 *
 * @Annotation
 */
class WidgetPlaceholderUnique extends Constraint
{
    /**
     * @var string
     */
    public $message = 'widget.placeholder_not_unique';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'darvin_content_widget_placeholder_unique';
    }
}
