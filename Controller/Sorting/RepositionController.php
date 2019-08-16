<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Controller\Sorting;

use Darvin\ContentBundle\Form\Type\Sorting\RepositionType;
use Darvin\ContentBundle\Sorting\Reposition\Model\Reposition;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reposition controller
 */
class RepositionController
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory Form factory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request): Response
    {
        $reposition = new Reposition();

        $form = $this->formFactory->create(RepositionType::class, $reposition)->handleRequest($request);

        $success = $form->isValid();

        $message = $success
            ? 'content.sorting.reposition.success'
            : implode(PHP_EOL, array_map(function (FormError $error) {
                return $error->getMessage();
            }, iterator_to_array($form->getErrors(true))));

        return new AjaxResponse('', $success, $message);
    }
}
