<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Event\Filterer;

use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Filterer build constraint event
 */
class BuildConstraintEvent extends Event
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var string
     */
    private $field;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $rootAlias;

    /**
     * @var bool
     */
    private $strictComparison;

    /**
     * @var string|null
     */
    private $constraint;

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder     Query builder
     * @param string                     $field            Field
     * @param mixed                      $value            Value
     * @param string                     $entityClass      Entity class
     * @param string                     $rootAlias        Query builder root alias
     * @param bool                       $strictComparison Whether to use strict comparison
     */
    public function __construct(QueryBuilder $queryBuilder, string $field, $value, string $entityClass, string $rootAlias, bool $strictComparison)
    {
        $this->queryBuilder = $queryBuilder;
        $this->field = $field;
        $this->value = $value;
        $this->entityClass = $entityClass;
        $this->rootAlias = $rootAlias;
        $this->strictComparison = $strictComparison;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @return string
     */
    public function getRootAlias(): string
    {
        return $this->rootAlias;
    }

    /**
     * @return bool
     */
    public function isStrictComparison(): bool
    {
        return $this->strictComparison;
    }

    /**
     * @return null|string
     */
    public function getConstraint(): ?string
    {
        return $this->constraint;
    }

    /**
     * @param null|string $constraint Constraint
     *
     * @return BuildConstraintEvent
     */
    public function setConstraint(?string $constraint): BuildConstraintEvent
    {
        $this->constraint = $constraint;

        return $this;
    }
}
