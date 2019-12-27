<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Autocomplete\Provider\Config\Model;

/**
 * Permission
 */
class Permission
{
    /**
     * @var mixed
     */
    private $attribute;

    /**
     * @var mixed|null
     */
    private $subject;

    /**
     * @param mixed      $attribute Attribute
     * @param mixed|null $subject   Subject
     */
    public function __construct($attribute, $subject = null)
    {
        $this->attribute = $attribute;
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return mixed|null
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
