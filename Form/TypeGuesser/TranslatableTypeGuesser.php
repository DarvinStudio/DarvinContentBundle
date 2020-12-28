<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Form\TypeGuesser;

use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;

/**
 * Translatable form type guesser
 */
class TranslatableTypeGuesser implements FormTypeGuesserInterface
{
    /**
     * @var \Symfony\Component\Form\FormTypeGuesserInterface
     */
    private $genericTypeGuesser;

    /**
     * @param \Symfony\Component\Form\FormTypeGuesserInterface $genericTypeGuesser Generic form type guesser
     */
    public function __construct(FormTypeGuesserInterface $genericTypeGuesser)
    {
        $this->genericTypeGuesser = $genericTypeGuesser;
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($class, $property): ?TypeGuess
    {
        return $this->guess($class, $property, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function guessRequired($class, $property): ?ValueGuess
    {
        return $this->guess($class, $property, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function guessMaxLength($class, $property): ?ValueGuess
    {
        return $this->guess($class, $property, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function guessPattern($class, $property): ?ValueGuess
    {
        return $this->guess($class, $property, __FUNCTION__);
    }

    /**
     * @param string $class    The fully qualified class name
     * @param string $property The name of the property to guess for
     * @param string $method   Guesser method
     *
     * @return \Symfony\Component\Form\Guess\Guess|null
     */
    private function guess(string $class, string $property, string $method): ?Guess
    {
        if (!is_a($class, TranslatableInterface::class, true)) {
            return null;
        }

        $firstGuess = $this->genericTypeGuesser->$method($class, $property);

        if (!$firstGuess instanceof Guess) {
            return null;
        }

        /** @var \Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface $class */
        $secondGuess = $this->genericTypeGuesser->$method($class::getTranslationEntityClass(), $property);

        if (!$secondGuess instanceof Guess) {
            return null;
        }

        return $firstGuess->getConfidence() > $secondGuess->getConfidence() ? $firstGuess : $secondGuess;
    }
}
