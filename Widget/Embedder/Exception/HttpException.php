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
     * @param string                                                $message             Message
     * @param int                                                   $code                Code
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $kernelHttpException Kernel HTTP exception
     */
    public function __construct($message = '', $code = 0, KernelHttpException $kernelHttpException)
    {
        parent::__construct($message, $code, $kernelHttpException);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Exception\HttpException $kernelHttpException Kernel HTTP exception
     *
     * @return HttpException
     */
    public static function create(KernelHttpException $kernelHttpException)
    {
        return new self('', 0, $kernelHttpException);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getKernelHttpException()
    {
        return $this->getPrevious();
    }
}
