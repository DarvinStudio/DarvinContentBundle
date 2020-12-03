<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener\Translatable;

use Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Set translatable locales event subscriber
 */
class SetLocalesSubscriber implements EventSubscriber
{
    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface
     */
    private $localeSetter;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface $localeSetter Translatable locale setter
     */
    public function __construct(TranslatableLocaleSetterInterface $localeSetter)
    {
        $this->localeSetter = $localeSetter;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::prePersist,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs Event arguments
     */
    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();

        if ($entity instanceof TranslatableInterface) {
            $this->localeSetter->setLocales($entity);
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs Event arguments
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();

        if ($entity instanceof TranslatableInterface) {
            $this->localeSetter->setLocales($entity);
        }
    }
}
