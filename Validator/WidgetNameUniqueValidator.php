<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Validator;

use Darvin\ContentBundle\Widget\WidgetPoolInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Widget name unique constraint validator
 */
class WidgetNameUniqueValidator extends ConstraintValidator
{
    /**
     * @var \Darvin\ContentBundle\Widget\WidgetPoolInterface
     */
    private $widgetPool;

    /**
     * @param \Darvin\ContentBundle\Widget\WidgetPoolInterface $widgetPool Widget pool
     */
    public function __construct(WidgetPoolInterface $widgetPool)
    {
        $this->widgetPool = $widgetPool;
    }

    /**
     * @param string                                                                                               $name       Widget name
     * @param \Darvin\ContentBundle\Validator\Constraints\WidgetNameUnique|\Symfony\Component\Validator\Constraint $constraint Constraint
     */
    public function validate($name, Constraint $constraint): void
    {
        if (!$this->widgetPool->isWidgetUnique($name)) {
            $this->context->addViolation($constraint->message, [
                '%name%' => $name,
            ]);
        }
    }
}
