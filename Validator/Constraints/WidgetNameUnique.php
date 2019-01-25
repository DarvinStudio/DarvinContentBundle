<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Widget name unique validation constraint
 *
 * @Annotation
 */
class WidgetNameUnique extends Constraint
{
    /**
     * @var string
     */
    public $message = 'widget.name_not_unique';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'darvin_content_widget_name_unique';
    }
}
