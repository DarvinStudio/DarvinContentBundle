<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
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
 * Widget placeholder unique constraint validator
 */
class WidgetPlaceholderUniqueValidator extends ConstraintValidator
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
     * @param string                                                                                                      $placeholder Widget placeholder
     * @param \Darvin\ContentBundle\Validator\Constraints\WidgetPlaceholderUnique|\Symfony\Component\Validator\Constraint $constraint  Constraint
     */
    public function validate($placeholder, Constraint $constraint)
    {
        if (!$this->widgetPool->isWidgetUnique($placeholder)) {
            $this->context->addViolation($constraint->message, array(
                '%placeholder%' => $placeholder,
            ));
        }
    }
}
