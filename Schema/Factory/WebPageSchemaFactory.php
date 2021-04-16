<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Schema\Factory;

use Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGeneratorInterface;
use Darvin\SchemaBundle\Factory\SchemaFactoryInterface;
use Darvin\SchemaBundle\Model\ReadAction;
use Darvin\SchemaBundle\Model\WebPage;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Web page schema factory
 */
class WebPageSchemaFactory implements WebPageSchemaFactoryInterface
{
    /**
     * @var \Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGeneratorInterface
     */
    protected $canonicalUrlGenerator;

    /**
     * @var \Darvin\SchemaBundle\Factory\SchemaFactoryInterface
     */
    protected $genericSchemaFactory;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @param \Darvin\ContentBundle\CanonicalUrl\CanonicalUrlGeneratorInterface $canonicalUrlGenerator Canonical URL generator
     * @param \Darvin\SchemaBundle\Factory\SchemaFactoryInterface               $genericSchemaFactory  Generic schema factory
     * @param \Symfony\Component\HttpFoundation\RequestStack                    $requestStack          Request stack
     */
    public function __construct(
        CanonicalUrlGeneratorInterface $canonicalUrlGenerator,
        SchemaFactoryInterface $genericSchemaFactory,
        RequestStack $requestStack
    ) {
        $this->canonicalUrlGenerator = $canonicalUrlGenerator;
        $this->genericSchemaFactory = $genericSchemaFactory;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function createWebPageSchema(
        string $name,
        ?string $description = null,
        ?\DateTime $dateModified = null,
        ?\DateTime $datePublished = null,
        ?string $url = null
    ): WebPage {
        if (null === $url) {
            $url = $this->canonicalUrlGenerator->generateCanonicalUrl();
        }

        $request = $this->requestStack->getCurrentRequest();

        /** @var \Darvin\SchemaBundle\Model\WebPage $schema */
        $schema = $this->genericSchemaFactory->createSchema(WebPage::class);
        $schema
            ->setName($name)
            ->setDescription($description)
            ->setDateModified($dateModified)
            ->setDatePublished($datePublished)
            ->setUrl($url)
            ->setInLanguage(null !== $request ? $request->getLocale() : null)
            ->setPotentialAction($this->getPotentialAction($url));

        return $schema;
    }

    /**
     * @param string|null $url URL
     *
     * @return \Darvin\SchemaBundle\Model\ReadAction|null
     */
    protected function getPotentialAction(?string $url): ?ReadAction
    {
        if (null === $url) {
            return null;
        }

        /** @var \Darvin\SchemaBundle\Model\ReadAction $schema */
        $schema = $this->genericSchemaFactory->createSchema(ReadAction::class);
        $schema->setTarget([$url]);

        return $schema;
    }
}
