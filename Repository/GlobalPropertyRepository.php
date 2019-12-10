<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Repository;

use Darvin\ContentBundle\Traits\TranslatableRepositoryTrait;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Global property entity repository
 */
class GlobalPropertyRepository extends EntityRepository
{
    use TranslatableRepositoryTrait;

    /**
     * @param string|null $locale Locale
     *
     * @return array
     */
    public function getValuesForPropertyEmbedder(?string $locale = null): array
    {
         $qb = $this->createDefaultBuilder();
         $qb
             ->select('o.name')
             ->addSelect('translations.value');
         $this->joinTranslations($qb, $locale, false);

         $values = [];

         foreach ($qb->getQuery()->getScalarResult() as $row) {
             $values[$row['name']] = $row['value'];
         }

         return $values;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createDefaultBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }
}
