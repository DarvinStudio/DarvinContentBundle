<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Tests\Property\Embedder;

use Darvin\ContentBundle\Property\Embedder\PropertyEmbedder;
use Darvin\ContentBundle\Repository\GlobalPropertyRepository;
use Darvin\Utils\Locale\LocaleProviderInterface;
use Darvin\Utils\Strings\Stringifier\StringifierInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Property embedder test
 *
 * @group property
 */
class PropertyEmbedderTest extends TestCase
{
    /**
     * @var \Darvin\ContentBundle\Property\Embedder\PropertyEmbedderInterface
     */
    private $embedder;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();

        $globalPropertyRepository = $this->getMockBuilder(GlobalPropertyRepository::class)->disableOriginalConstructor()->getMock();
        $globalPropertyRepository->method('getValuesForPropertyEmbedder')->willReturn([
            'global_1' => 'foo',
            'global_2' => 'bar',
        ]);

        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->method('getRepository')->willReturn($globalPropertyRepository);

        $localeProvider = $this->getMockBuilder(LocaleProviderInterface::class)->getMock();

        $propertyAccessor = $this->getMockBuilder(PropertyAccessorInterface::class)->getMock();
        $propertyAccessor->method('getValue')->willReturnCallback(function (object $object, string $property): string {
            return $property;
        });

        $stringifier = $this->getMockBuilder(StringifierInterface::class)->getMock();
        $stringifier->method('stringify')->willReturnArgument(0);

        $this->embedder = new PropertyEmbedder($container, $em, $localeProvider, $propertyAccessor, $stringifier, [
            'Stub' => [
                'callback_property' => [
                    'service' => __CLASS__,
                    'method'  => 'embedPropertiesCallback',
                ],
            ],
        ]);
    }

    /**
     * @dataProvider embedPropertiesProvider
     *
     * @param mixed $expected Expected result
     * @param mixed $content  Content
     * @param mixed $object   Object
     */
    public function testEmbedProperties($expected, $content, $object = null): void
    {
        self::assertEquals($expected, $this->embedder->embedProperties($content, $object));
    }

    /**
     * @return string
     */
    public static function embedPropertiesCallback()
    {
        return 'Callback Result';
    }

    /**
     * @return iterable
     */
    public function embedPropertiesProvider(): iterable
    {
        $stub = $this->getMockBuilder('Stub')->getMock();

        yield ['', null];
        yield ['test', 'test'];
        yield ['Hello, %world%!', 'Hello, %world%!'];
        yield ['Hello, World!', 'Hello, %world%!', $stub];
        yield ['Test', '%test%', $stub];
        yield ['%test', '%test', $stub];
        yield ['TestTest', '%test%%test%', $stub];
        yield ['foo bar', '%global_1% %global_2%'];
        yield ['foo bar', '%gLoBaL_1% %GlObAl_2%'];
        yield ['bar bar', '%gLoBaL_2% %GlObAl_2%'];
        yield ['Global1 Global2', '%global_1% %global_2%', $stub];
        yield ['Callback Result', '%callback_property%', $stub];
    }
}
