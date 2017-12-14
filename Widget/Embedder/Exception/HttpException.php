<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Widget\Embedder\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException as KernelHttpException;

/**
 * Widget embedder HTTP exception
 */
class HttpException extends WidgetEmbedderException
{
    /**
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $kernelHttpException Kernel HTTP exception
     */
    public function __construct(KernelHttpException $kernelHttpException)
    {
        parent::__construct('', 0, $kernelHttpException);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getKernelHttpException()
    {
        return $this->getPrevious();
    }
}
