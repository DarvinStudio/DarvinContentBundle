<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Form\TypeGuesser;

use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
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
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @var \Symfony\Component\Form\FormTypeGuesserInterface
     */
    private $genericTypeGuesser;

    /**
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     * @param \Symfony\Component\Form\FormTypeGuesserInterface                $genericTypeGuesser  Generic form type guesser
     */
    public function __construct(TranslatableManagerInterface $translatableManager, FormTypeGuesserInterface $genericTypeGuesser)
    {
        $this->translatableManager = $translatableManager;
        $this->genericTypeGuesser = $genericTypeGuesser;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property): ?TypeGuess
    {
        return $this->guess($class, $property, __FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function guessRequired($class, $property): ?ValueGuess
    {
        return $this->guess($class, $property, __FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function guessMaxLength($class, $property): ?ValueGuess
    {
        return $this->guess($class, $property, __FUNCTION__);
    }

    /**
     * {@inheritdoc}
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
        if (!$this->translatableManager->isTranslatable($class)) {
            return null;
        }

        $firstGuess = $this->genericTypeGuesser->$method($class, $property);

        if (!$firstGuess instanceof Guess) {
            return null;
        }

        $secondGuess = $this->genericTypeGuesser->$method($this->translatableManager->getTranslationClass($class), $property);

        if (!$secondGuess instanceof Guess) {
            return null;
        }

        return $firstGuess->getConfidence() > $secondGuess->getConfidence() ? $firstGuess : $secondGuess;
    }
}
